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
    public ?int $openProjectId = null;

    public int $activeImage = 0;

    public bool $featuredOnly = false;

    #[Url(as: 'servicio', except: '')]
    public ?string $selectedService = null;

    public function mount(bool $featuredOnly = false): void
    {
        $this->featuredOnly = $featuredOnly;

        if ($this->featuredOnly) {
            $this->selectedService = null;
        }
    }

    public function selectService(string $slug): void
    {
        if ($this->featuredOnly) {
            return;
        }

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

        if ($serviceSlug) {
            $q->whereHas('service', fn ($sq) => $sq->where('slug', $serviceSlug));
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
     * Hub cards: one per active service with cover + project count.
     *
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

    public function render()
    {
        $showHub = $this->featuredOnly || blank($this->selectedService);
        $hubCards = $showHub ? $this->serviceHubCards() : collect();

        $activeService = null;
        $projects = collect();

        if (! $showHub && $this->selectedService) {
            $activeService = Service::query()
                ->active()
                ->where('slug', $this->selectedService)
                ->first();

            if (! $activeService) {
                $this->selectedService = null;
                $showHub = true;
                $hubCards = $this->serviceHubCards();
            } else {
                $projects = $this->projectsQuery($activeService->slug)->get();
            }
        }

        if ($this->featuredOnly) {
            $mapSource = $this->projectsQuery()->get();
        } else {
            $mapSource = $showHub
                ? $this->projectsQuery()->get()
                : $projects;
        }

        $openProject = $this->resolvedOpenProject();
        $gallery = $openProject ? $this->galleryFor($openProject) : [];

        if ($gallery && $this->activeImage >= count($gallery)) {
            $this->activeImage = 0;
        }

        return view('livewire.project-gallery', [
            'showHub' => $showHub,
            'hubCards' => $hubCards,
            'activeService' => $activeService,
            'projects' => $projects,
            'openProject' => $openProject,
            'gallery' => $gallery,
            'mapProjects' => $mapSource
                ->filter(fn ($p) => $p->latitude !== null && $p->longitude !== null)
                ->map->toMapPayload()
                ->values(),
        ]);
    }
}
