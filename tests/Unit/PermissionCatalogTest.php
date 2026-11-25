<?php

namespace Tests\Unit;

use App\Support\PermissionCatalog;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Prueba UNITARIA: verifica una función aislada, sin base de datos ni HTTP.
 * Es la prueba más rápida que existe (milisegundos).
 */
class PermissionCatalogTest extends TestCase
{
    /**
     * Data provider: la misma prueba se ejecuta con varios casos.
     */
    public static function nombres(): array
    {
        return [
            'permiso simple' => ['ver-productos', ['ver', 'productos']],
            'acción propia' => ['recibir-compras', ['recibir', 'compras']],
            'módulo con guion' => ['ver-ordenes-servicio', ['ver', 'ordenes-servicio']],
            'sin guion' => ['administrar', ['administrar', 'otros']],
        ];
    }

    #[DataProvider('nombres')]
    public function test_separa_accion_y_modulo(string $permiso, array $esperado): void
    {
        $this->assertSame($esperado, PermissionCatalog::split($permiso));
    }
}
