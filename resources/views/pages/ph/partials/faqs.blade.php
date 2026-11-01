{{-- $faqs: [['question' => ..., 'answer' => ...]] --}}
<section class="sd-block" id="faq" aria-labelledby="ph-faq">
    <header class="sd-block-head">
        <span class="sd-block-tag">Preguntas frecuentes</span>
        <h2 class="sd-block-title" id="ph-faq">{{ $titulo ?? 'Lo que preguntan los administradores' }}</h2>
    </header>
    <div class="ph-faqs">
        @foreach($faqs as $faq)
            <details class="ph-faq" @if($loop->first) open @endif>
                <summary>{{ $faq['question'] }}</summary>
                <p>{{ $faq['answer'] }}</p>
            </details>
        @endforeach
    </div>
</section>
