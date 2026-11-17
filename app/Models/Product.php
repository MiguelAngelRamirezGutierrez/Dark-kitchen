<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Producto
{
    /** @use HasFactory<ProductFactory> */
    use Auditable, HasFactory, SoftDeletes;

    // Auditoría (Clase 12)
    protected string $auditLog = 'productos';

    // El stock NO se audita aquí: sus cambios quedan en el kardex (inventory_movements)
    protected array $auditExcept = ['stock'];

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'category_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'estado',
        'name',
        'description',
        'price',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'estado' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes['categoria_id'] ?? $this->attributes['category_id'] ?? null;
    }

    public function setCategoryIdAttribute($value)
    {
        $this->attributes['categoria_id'] = $value;
    }

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

    // Relación: movimientos del kardex (Clase 12)
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_id');
    }
}
