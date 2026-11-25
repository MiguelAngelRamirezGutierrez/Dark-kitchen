<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Plantilla para la importación masiva: el usuario la descarga, la llena y la vuelve a subir.
 * Los encabezados deben coincidir con las claves que espera ProductsImport.
 */
class ProductsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function headings(): array
    {
        return ['nombre', 'descripcion', 'precio', 'stock', 'categoria'];
    }

    public function array(): array
    {
        return [
            ['Tablet Samsung A9', 'Tablet 8.7 pulgadas 64 GB', 780000, 5, 'Computación'],
            ['Cargador USB-C 25W', 'Cargador rápido', 65000, 20, 'Accesorios'],
        ];
    }
}
