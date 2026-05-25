@if(count($logos) > 0)
    <div class="clients-ticker-bar" aria-label="Empresas con las que hemos trabajado">
        <div class="clients-ticker-label">
            <span>EMPRESAS QUE CONFÍAN EN NOSOTROS:</span>
        </div>
        <div class="clients-ticker-viewport">
            <div class="clients-ticker-track">
                @foreach($logos as $logo)
                    <div class="clients-ticker-item">
                        <img src="{{ $logo['url'] }}" alt="{{ $logo['name'] }}" loading="lazy" />
                    </div>
                @endforeach
                @foreach($logos as $logo)
                    <div class="clients-ticker-item" aria-hidden="true">
                        <img src="{{ $logo['url'] }}" alt="" loading="lazy" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>document.body.classList.add('has-clients-ticker');</script>
@endif
