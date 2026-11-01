<section class="sd-block" aria-labelledby="ph-proc">
    <header class="sd-block-head">
        <span class="sd-block-tag">Cómo trabajamos</span>
        <h2 class="sd-block-title" id="ph-proc">Del diagnóstico a la entrega, en 4 pasos</h2>
    </header>
    <ol class="ph-steps">
        @foreach(config('propiedad-horizontal.proceso') as $step)
            <li class="ph-step">
                <span class="ph-step__num">{{ $loop->iteration }}</span>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['text'] }}</p>
            </li>
        @endforeach
    </ol>
</section>
