{{-- Tarjeta flotante: vista previa del informe de diagnóstico (sin cifras inventadas) --}}
<div class="ph-report" aria-hidden="true">
    <div class="ph-report__head">
        <span class="ph-report__icon"><x-ph-icon name="file" :size="18" /></span>
        <div>
            <strong>Informe de diagnóstico</strong>
            <span>PDF para el consejo de administración</span>
        </div>
    </div>
    <ul class="ph-report__list">
        <li><span class="ph-dot ph-dot--crit"></span>Puntos ciegos en parqueaderos</li>
        <li><span class="ph-dot ph-dot--warn"></span>Estado de cámaras y grabación</li>
        <li><span class="ph-dot ph-dot--warn"></span>Control de accesos y visitantes</li>
        <li><span class="ph-dot ph-dot--good"></span>3 opciones de mejora con valor</li>
    </ul>
    <div class="ph-report__foot">
        <span><x-ph-icon name="clock" :size="14" /> Recorrido de 45 min</span>
        <span class="ph-report__free">Gratis</span>
    </div>
</div>
