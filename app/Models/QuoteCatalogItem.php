<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteCatalogItem extends Model
{
    public const TYPES = [
        'product' => 'Producto',
        'service' => 'Servicio',
        'outsourcing' => 'Outsourcing',
        'license' => 'Licencia',
        'subscription' => 'Suscripción',
        'project' => 'Proyecto',
    ];

    public const UNITS = [
        'unidad' => 'Unidad',
        'metro' => 'Metro',
        'punto' => 'Punto',
        'hora' => 'Hora',
        'dia' => 'Día',
        'mes' => 'Mes',
        'servicio' => 'Servicio',
        'proyecto' => 'Proyecto',
        'licencia' => 'Licencia',
    ];

    protected $fillable = [
        'type',
        'code',
        'name',
        'description',
        'unit',
        'default_unit_price',
        'default_tax_rate',
        'category',
        'is_active',
    ];

    protected $casts = [
        'default_unit_price' => 'decimal:2',
        'default_tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
