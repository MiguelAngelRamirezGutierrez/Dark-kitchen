<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Producto
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'estado',
        'name',
        'price',
        'active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoria_id')->withTrashed();
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'product_id');
    }

    // Relación: un producto aparece en muchos detalles de compra (Clase 10)
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class, 'product_id');
    }
}
