<?php

namespace App\Http\Requests;

// Usa las mismas reglas que StoreProductoRequest excepto stock (Clase 12)
class UpdateProductoRequest extends StoreProductoRequest
{
    /**
     * Clase 12: al editar NO se recibe el stock. Solo cambia por movimientos
     * (compras, ventas, anulaciones o ajustes con motivo), que quedan en el kardex.
     */
    public function rules(): array
    {
        return collect(parent::rules())->except('stock')->all();
    }
}
