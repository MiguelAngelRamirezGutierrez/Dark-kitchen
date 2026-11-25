<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Database\Factories\ProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    /** @use HasFactory<ProviderFactory> */
    use Auditable, HasFactory, SoftDeletes;

    // Auditoría (Clase 12)
    protected string $auditLog = 'proveedores';

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    // Relación: un proveedor tiene muchas compras
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
