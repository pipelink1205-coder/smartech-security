<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectGallery extends Component
{
    public ?int $selectedId = null;

    public int $activeImage = 0;

    public bool $featuredOnly = false;

    public function mount(bool $featuredOnly = false): void
    {
        $this->featuredOnly = $featuredOnly;
        $this->selectedId = $this->projectsQuery()->value('id');
    }

    public function selectProject(int $id): void
    {
        $this->selectedId = $id;
        $this->activeImage = 0;
    }

    #[On('map-select-project')]
    public function onMapSelectProject(int $id): void
    {
        $this->selectProject($id);
    }

    public function selectImage(int $index): void
    {
        $this->activeImage = max(0, $index);
    }

    protected function projectsQuery()
    {
        $q = Project::query()->with(['images' => fn ($q) => $q->orderBy('sort_order')]);

        if ($this->featuredOnly) {
            $q->featured();
        }

        return $q->latest('year')->latest('id');
    }

    public function render()
    {
        $projects = $this->projectsQuery()->get();
        $selected = $projects->firstWhere('id', $this->selectedId) ?? $projects->first();

        if ($selected && $this->selectedId !== $selected->id) {
            $this->selectedId = $selected->id;
        }

        $gallery = $selected ? $this->galleryFor($selected) : [];

        if ($gallery && $this->activeImage >= count($gallery)) {
            $this->activeImage = 0;
        }

        return view('livewire.project-gallery', [
            'projects'    => $projects,
            'selected'    => $selected,
            'gallery'     => $gallery,
            'mapProjects' => $projects->filter(fn ($p) => $p->latitude && $p->longitude)->map->toMapPayload()->values(),
        ]);
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
}
