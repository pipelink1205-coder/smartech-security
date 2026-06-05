@php
    $sections = config('site.sections', []);
@endphp

@if(count($sections))
<nav class="section-rail" id="sectionRail" aria-label="Secciones de la página">
    @foreach($sections as $section)
        <a
            href="#{{ $section['id'] }}"
            class="section-rail__dot"
            data-section="{{ $section['id'] }}"
            data-page-link="{{ $section['id'] }}"
            title="{{ $section['label'] }}"
            aria-label="{{ $section['label'] }}"
        >
            <span class="section-rail__label">{{ $section['label'] }}</span>
        </a>
    @endforeach
</nav>
@endif
