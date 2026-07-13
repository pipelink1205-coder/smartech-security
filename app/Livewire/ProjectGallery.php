<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectGallery extends Component
{
    public ?int $selectedService = null;

    public ?int $openProjectId = null;

    public int $activeImage = 0;

    public bool $featuredOnly = false;

    public function mount(bool $featuredOnly = false): void
    {
        $this->featuredOnly = $featuredOnly;
    }

    public function showAllServices(): void
    {
        $this->filterService(null);
    }

    public function filterService(?int $id): void
    {
        $this->selectedService = $id;
        $this->openProjectId = null;
        $this->activeImage = 0;
    }

    public function openProject(int $id): void
    {
        $this->openProjectId = $id;
        $this->activeImage = 0;
    }

    public function closeProject(): void
    {
        $this->openProjectId = null;
        $this->activeImage = 0;
    }

    #[On('map-select-project')]
    public function onMapSelectProject(int $id): void
    {
        $this->openProject($id);
    }

    #[On('close-project-lightbox')]
    public function closeProjectFromEvent(): void
    {
        $this->closeProject();
    }

    public function selectImage(int $index): void
    {
        $this->activeImage = max(0, $index);
    }

    public function nextImage(): void
    {
        $project = $this->resolvedOpenProject();
        if (! $project) {
            return;
        }

        $count = count($this->galleryFor($project));
        if ($count <= 1) {
            return;
        }

        $this->activeImage = ($this->activeImage + 1) % $count;
    }

    public function prevImage(): void
    {
        $project = $this->resolvedOpenProject();
        if (! $project) {
            return;
        }

        $count = count($this->galleryFor($project));
        if ($count <= 1) {
            return;
        }

        $this->activeImage = ($this->activeImage - 1 + $count) % $count;
    }

    protected function projectsQuery()
    {
        $q = Project::query()->with([
            'service',
            'images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order'),
        ]);

        if ($this->featuredOnly) {
            $q->featured();
        }

        if ($this->selectedService) {
            $q->where('service_id', $this->selectedService);
        }

        return $q
            ->orderByDesc('is_featured')
            ->orderByDesc('year')
            ->orderByDesc('id');
    }

    protected function resolvedOpenProject(): ?Project
    {
        if (! $this->openProjectId) {
            return null;
        }

        return Project::query()
            ->with([
                'service',
                'images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order'),
            ])
            ->find($this->openProjectId);
    }

    /** @return array<int, array{url: string, caption: ?string}> */
    protected function galleryFor(Project $project): array
    {
        if ($project->images->isNotEmpty()) {
            return $project->images->map(fn ($img) => [
                'url'     => $img->url,
                'caption' => $img->caption,
            ])->all();
        }

        return [['url' => $project->image_url, 'caption' => null]];
    }

    public function render()
    {
        $projects = $this->projectsQuery()->get();

        $services = Service::query()
            ->active()
            ->ordered()
            ->whereHas('projects', function ($q) {
                if ($this->featuredOnly) {
                    $q->featured();
                }
            })
            ->withCount(['projects' => function ($q) {
                if ($this->featuredOnly) {
                    $q->featured();
                }
            }])
            ->get();

        $openProject = $this->resolvedOpenProject();
        $gallery = $openProject ? $this->galleryFor($openProject) : [];

        if ($gallery && $this->activeImage >= count($gallery)) {
            $this->activeImage = 0;
        }

        $totalProjectsCount = Project::query()
            ->when($this->featuredOnly, fn ($q) => $q->featured())
            ->count();

        $currentYear = (int) date('Y');

        $projectBlocks = [
            [
                'key'   => 'destacados',
                'label' => 'Destacados',
                'hint'  => 'Proyectos principales en el sitio',
                'items' => $projects->where('is_featured', true)->values(),
            ],
            [
                'key'   => 'recientes',
                'label' => 'Recientes',
                'hint'  => 'Trabajos de los últimos años',
                'items' => $projects
                    ->where('is_featured', false)
                    ->filter(fn (Project $p) => (int) ($p->year ?? 0) >= $currentYear - 1)
                    ->values(),
            ],
            [
                'key'   => 'realizados',
                'label' => 'Más proyectos realizados',
                'hint'  => 'Historos anteriores',
                'items' => $projects
                    ->where('is_featured', false)
                    ->filter(fn (Project $p) => (int) ($p->year ?? 0) < $currentYear - 1)
                    ->values(),
            ],
        ];

        // Si un solo bloque tiene todo (p. ej. solo destacados en inicio), no repetir títulos vacíos.
        $projectBlocks = array_values(array_filter(
            $projectBlocks,
            static fn (array $block): bool => $block['items']->isNotEmpty()
        ));

        return view('livewire.project-gallery', [
            'projects'           => $projects,
            'projectBlocks'      => $projectBlocks,
            'services'           => $services,
            'totalProjectsCount' => $totalProjectsCount,
            'openProject'        => $openProject,
            'gallery'            => $gallery,
            'mapProjects'        => $projects
                ->filter(fn ($p) => $p->latitude !== null && $p->longitude !== null)
                ->map->toMapPayload()
                ->values(),
        ]);
    }
}
