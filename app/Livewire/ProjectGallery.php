<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectGallery extends Component
{
    /** Slug del servicio activo (filtro PC / detalle móvil). */
    #[Url(as: 'servicio', except: '')]
    public ?string $selectedService = null;

    public ?int $openProjectId = null;

    public int $activeImage = 0;

    public bool $featuredOnly = false;

    public function mount(bool $featuredOnly = false): void
    {
        $this->featuredOnly = $featuredOnly;

        if ($this->featuredOnly) {
            $this->selectedService = null;
        }
    }

    public function showAllServices(): void
    {
        $this->selectedService = null;
        $this->closeProject();
    }

    public function filterService(int $id): void
    {
        $slug = Service::query()->whereKey($id)->value('slug');
        $this->selectedService = $slug ?: null;
        $this->closeProject();
    }

    public function selectService(string $slug): void
    {
        $this->selectedService = $slug;
        $this->closeProject();
    }

    public function clearService(): void
    {
        $this->selectedService = null;
        $this->closeProject();
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

    protected function projectsQuery(?string $serviceSlug = null)
    {
        $q = Project::query()->with([
            'service',
            'images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order'),
        ]);

        if ($this->featuredOnly) {
            $q->featured();
        }

        $slug = $serviceSlug ?? $this->selectedService;
        if ($slug) {
            $q->whereHas('service', fn ($sq) => $sq->where('slug', $slug));
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

    /**
     * @return array<int, array{url: string, caption: ?string}>
     */
    protected function galleryFor(Project $project): array
    {
        if ($project->images->isNotEmpty()) {
            return $project->images->map(fn ($img) => [
                'url' => $img->url,
                'caption' => $img->caption,
            ])->all();
        }

        return [['url' => $project->image_url, 'caption' => null]];
    }

    /**
     * @return Collection<int, array{key: string, label: string, icon: ?string, count: int, cover: string, href: ?string}>
     */
    protected function serviceHubCards(): Collection
    {
        $services = Service::query()->active()->ordered()->get();

        $counts = Project::query()
            ->whereNotNull('service_id')
            ->selectRaw('service_id, COUNT(*) as aggregate')
            ->groupBy('service_id')
            ->pluck('aggregate', 'service_id');

        $covers = Project::query()
            ->with(['images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')])
            ->whereNotNull('service_id')
            ->orderByDesc('is_featured')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get()
            ->unique('service_id')
            ->keyBy('service_id');

        return $services->map(function (Service $service) use ($counts, $covers) {
            $count = (int) ($counts[$service->id] ?? 0);
            $first = $covers->get($service->id);

            return [
                'key' => $service->slug,
                'label' => $service->name,
                'icon' => $service->icon,
                'count' => $count,
                'cover' => $first?->image_url ?? $service->image_url,
                'href' => $count > 0
                    ? route('proyectos', ['servicio' => $service->slug])
                    : null,
            ];
        });
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @return array<int, array{key: string, label: string, hint: string, items: Collection<int, Project>}>
     */
    protected function projectBlocks(Collection $projects): array
    {
        $currentYear = (int) date('Y');

        $blocks = [
            [
                'key' => 'destacados',
                'label' => 'Destacados',
                'hint' => 'Proyectos principales en el sitio',
                'items' => $projects->where('is_featured', true)->values(),
            ],
            [
                'key' => 'recientes',
                'label' => 'Recientes',
                'hint' => 'Trabajos de los últimos años',
                'items' => $projects
                    ->where('is_featured', false)
                    ->filter(fn (Project $p) => (int) ($p->year ?? 0) >= $currentYear - 1)
                    ->values(),
            ],
            [
                'key' => 'realizados',
                'label' => 'Más proyectos realizados',
                'hint' => 'Trabajos anteriores',
                'items' => $projects
                    ->where('is_featured', false)
                    ->filter(fn (Project $p) => (int) ($p->year ?? 0) < $currentYear - 1)
                    ->values(),
            ],
        ];

        return array_values(array_filter(
            $blocks,
            static fn (array $block): bool => $block['items']->isNotEmpty()
        ));
    }

    public function render()
    {
        $projects = $this->projectsQuery()->get();
        $projectBlocks = $this->projectBlocks($projects);
        $hubCards = $this->serviceHubCards();

        $activeService = null;
        if ($this->selectedService) {
            $activeService = Service::query()
                ->active()
                ->where('slug', $this->selectedService)
                ->first();

            if (! $activeService) {
                $this->selectedService = null;
            }
        }

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

        $totalProjectsCount = Project::query()
            ->when($this->featuredOnly, fn ($q) => $q->featured())
            ->count();

        $openProject = $this->resolvedOpenProject();
        $gallery = $openProject ? $this->galleryFor($openProject) : [];

        if ($gallery && $this->activeImage >= count($gallery)) {
            $this->activeImage = 0;
        }

        $showMobileHub = blank($this->selectedService);

        return view('livewire.project-gallery', [
            'projects' => $projects,
            'projectBlocks' => $projectBlocks,
            'hubCards' => $hubCards,
            'showMobileHub' => $showMobileHub,
            'activeService' => $activeService,
            'services' => $services,
            'totalProjectsCount' => $totalProjectsCount,
            'openProject' => $openProject,
            'gallery' => $gallery,
            'mapProjects' => $projects
                ->filter(fn ($p) => $p->latitude !== null && $p->longitude !== null)
                ->map->toMapPayload()
                ->values(),
        ]);
    }
}
