<div class="ph-form">
    @if($submitted)
        <div class="ph-form__success" role="status">
            <span class="ph-form__success-icon"><x-ph-icon name="check" :size="28" /></span>
            <h3>¡Solicitud recibida!</h3>
            <p>Le escribiremos por WhatsApp para acordar el día del recorrido. Si dejó correo, también le llegará la confirmación.</p>
            <button type="button" wire:click="$set('submitted', false)" class="btn btn-ghost">Enviar otra solicitud</button>
        </div>
    @else
        <div class="ph-form__head">
            <h3>Agende el diagnóstico gratuito</h3>
            <p>Tarda menos de un minuto. Los campos con * son obligatorios.</p>
        </div>

        <form wire:submit="submit" novalidate>
            <div class="ph-hp" aria-hidden="true">
                <label for="ph-website">No llenar este campo</label>
                <input type="text" id="ph-website" wire:model="website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="ph-form__row">
                <div class="ph-field">
                    <label for="ph-name">Nombre *</label>
                    <input type="text" id="ph-name" wire:model="name" autocomplete="name" placeholder="Nombre y apellido" />
                    @error('name') <span class="ph-error">{{ $message }}</span> @enderror
                </div>
                <div class="ph-field">
                    <label for="ph-role">Su rol *</label>
                    <select id="ph-role" wire:model="role">
                        <option value="">Seleccione…</option>
                        @foreach($roles as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="ph-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="ph-form__row">
                <div class="ph-field">
                    <label for="ph-conjunto">Unidad, conjunto o edificio *</label>
                    <input type="text" id="ph-conjunto" wire:model="conjunto" placeholder="Ej.: Unidad Residencial Los Pinos" />
                    @error('conjunto') <span class="ph-error">{{ $message }}</span> @enderror
                </div>
                <div class="ph-field">
                    <label for="ph-municipio">Municipio *</label>
                    <select id="ph-municipio" wire:model="municipio">
                        <option value="">Seleccione…</option>
                        @foreach($municipios as $slug => $nombre)
                            <option value="{{ $slug }}">{{ $nombre }}</option>
                        @endforeach
                        <option value="otro">Otro municipio (con costo de desplazamiento)</option>
                    </select>
                    @error('municipio') <span class="ph-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <fieldset class="ph-field ph-chips">
                <legend>¿Qué le interesa revisar?</legend>
                <div class="ph-chips__list">
                    @foreach($servicios as $slug => $srv)
                        <label class="ph-chip">
                            <input type="checkbox" value="{{ $slug }}" wire:model="needs" />
                            <span><x-ph-icon :name="$srv['icon']" :size="16" /> {{ $srv['name'] }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="ph-form__row">
                <div class="ph-field">
                    <label for="ph-phone">WhatsApp / teléfono *</label>
                    <input type="tel" id="ph-phone" wire:model="phone" autocomplete="tel" placeholder="300 000 0000" />
                    @error('phone') <span class="ph-error">{{ $message }}</span> @enderror
                </div>
                <div class="ph-field">
                    <label for="ph-units">Número de apartamentos</label>
                    <select id="ph-units" wire:model="units">
                        <option value="">Seleccione…</option>
                        @foreach($unitOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ph-field">
                <label for="ph-email">Correo electrónico</label>
                <input type="email" id="ph-email" wire:model="email" autocomplete="email" placeholder="administracion@unidad.com" />
                @error('email') <span class="ph-error">{{ $message }}</span> @enderror
            </div>

            <div class="ph-field">
                <label for="ph-message">¿Qué está pasando en la unidad?</label>
                <textarea id="ph-message" wire:model="message" rows="3" placeholder="Ej.: robaron dos bicicletas del sótano y las cámaras no grabaron"></textarea>
                @error('message') <span class="ph-error">{{ $message }}</span> @enderror
            </div>

            <label class="ph-consent">
                <input type="checkbox" wire:model="consent" />
                <span>Autorizo a Smart Tech Security a tratar mis datos para contactarme sobre esta solicitud, según la <a href="{{ route('privacidad') }}" target="_blank" rel="noopener">política de tratamiento de datos</a> (Ley 1581 de 2012). *</span>
            </label>
            @error('consent') <span class="ph-error">{{ $message }}</span> @enderror

            <button type="submit" class="btn btn-primary ph-form__submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">Solicitar diagnóstico gratuito</span>
                <span wire:loading wire:target="submit">Enviando…</span>
            </button>
        </form>
    @endif
</div>
