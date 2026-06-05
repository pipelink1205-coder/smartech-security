@if(file_exists(public_path('images/logo.png')))
    <div class="brand-watermark" aria-hidden="true">
        <img
            src="{{ asset('images/logo.png') }}"
            alt=""
            class="brand-watermark__img"
            loading="lazy"
            decoding="async"
        />
    </div>
@endif
