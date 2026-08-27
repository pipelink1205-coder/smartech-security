@php
    $savedSignature = $record?->authorized_signature_data_uri;
    $isRep = (bool) ($get('is_legal_representative') ?? $record?->is_legal_representative);
@endphp

<div class="employee-signature-pad">
    <p class="employee-signature-pad__hint">
        @if($isRep)
            Dibuja aquí la firma, o cárgala abajo. Si subes una foto con fondo, al guardar se deja solo el trazo.
        @else
            Esta firma no se imprime. Solo aparece en los carnets si marcas a esta persona como representante legal.
        @endif
    </p>

    @if($savedSignature)
        <div class="employee-signature-pad__saved">
            <span>Firma guardada</span>
            <img src="{{ $savedSignature }}" alt="Firma guardada">
        </div>
    @endif

    <div
        wire:ignore
        x-data="{
            drawing: false,
            init() {
                const canvas = this.$refs.pad
                const ctx = canvas.getContext('2d')
                ctx.strokeStyle = '#0c2332'
                ctx.lineWidth = 2.2
                ctx.lineCap = 'round'
                ctx.lineJoin = 'round'

                const point = (event) => {
                    const rect = canvas.getBoundingClientRect()
                    const source = event.touches ? event.touches[0] : event
                    return {
                        x: (source.clientX - rect.left) * (canvas.width / rect.width),
                        y: (source.clientY - rect.top) * (canvas.height / rect.height),
                    }
                }

                const start = (event) => {
                    this.drawing = true
                    const { x, y } = point(event)
                    ctx.beginPath()
                    ctx.moveTo(x, y)
                    event.preventDefault()
                }

                const move = (event) => {
                    if (! this.drawing) return
                    const { x, y } = point(event)
                    ctx.lineTo(x, y)
                    ctx.stroke()
                    event.preventDefault()
                }

                const end = () => {
                    if (! this.drawing) return
                    this.drawing = false
                    $wire.set('data.signature_drawn', canvas.toDataURL('image/png'))
                }

                canvas.addEventListener('mousedown', start)
                canvas.addEventListener('mousemove', move)
                window.addEventListener('mouseup', end)
                canvas.addEventListener('touchstart', start, { passive: false })
                canvas.addEventListener('touchmove', move, { passive: false })
                canvas.addEventListener('touchend', end)
            },
            clear() {
                const canvas = this.$refs.pad
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height)
                $wire.set('data.signature_drawn', null)
            }
        }"
    >
        <canvas x-ref="pad" width="640" height="180" class="employee-signature-pad__canvas"></canvas>
        <button type="button" class="employee-signature-pad__clear" x-on:click="clear()">Borrar trazo</button>
    </div>
</div>

<style>
    .employee-signature-pad { display: grid; gap: .55rem; }
    .employee-signature-pad__hint { margin: 0; color: #4b5d66; font-size: .85rem; line-height: 1.4; }
    .employee-signature-pad__saved {
        display: grid;
        gap: .35rem;
        padding: .65rem .75rem;
        border: 1px solid #d7e8e4;
        border-radius: .7rem;
        background: #fff;
    }
    .employee-signature-pad__saved span {
        color: #0b6b5f;
        font-size: .72rem;
        font-weight: 750;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .employee-signature-pad__saved img {
        display: block;
        max-width: 16rem;
        max-height: 3.5rem;
        object-fit: contain;
    }
    .employee-signature-pad__canvas {
        width: 100%;
        height: 9.5rem;
        border: 1px solid #c5ddd8;
        border-radius: .75rem;
        background:
            linear-gradient(180deg, transparent 72%, #d7e8e4 73%, transparent 74%),
            #f8fbfa;
        cursor: crosshair;
        touch-action: none;
    }
    .employee-signature-pad__clear {
        display: inline-block;
        margin-top: .55rem;
        padding: .35rem .7rem;
        border: 0;
        border-radius: .5rem;
        background: #e8f4f1;
        color: #0b6b5f;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
    }
</style>
