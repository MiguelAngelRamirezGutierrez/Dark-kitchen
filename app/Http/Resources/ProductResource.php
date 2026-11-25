<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Define QUÉ datos de un producto salen por la API y con qué nombres.
 * Así se evita exponer columnas internas (deleted_at, etc.) y el formato queda estable.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'descripcion' => $this->description,
            'precio' => (float) $this->price,
            'stock' => $this->stock,
            'disponible' => $this->active && $this->stock > 0,
            'categoria' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'nombre' => $this->category?->name,
            ]),
        ];
    }
}
