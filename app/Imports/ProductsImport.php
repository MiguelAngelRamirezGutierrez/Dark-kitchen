<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Importación masiva de productos desde Excel/CSV.
 * - WithHeadingRow: la primera fila trae los nombres de columna (nombre, precio, ...).
 * - WithValidation: cada fila se valida con reglas de Laravel.
 * - SkipsOnFailure + SkipsFailures: las filas con errores se OMITEN y se reportan; las demás se guardan.
 * Regla de negocio: si el producto ya existe (mismo nombre) se actualizan precio, descripción y categoría,
 * pero NUNCA el stock (el stock solo cambia por movimientos del kardex).
 */
class ProductsImport implements SkipsOnFailure, ToCollection, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public int $created = 0;

    public int $updated = 0;

    public function __construct(private InventoryService $inventory) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $category = Category::where('nombre', trim((string) $row['categoria']))->first();
            // Look up by both aliases (name = accessor for 'nombre')
            $product = Product::where('nombre', trim((string) $row['nombre']))->first();

            if ($product) {
                $product->update([
                    'descripcion' => $row['descripcion'] ?? $product->descripcion,
                    'precio' => $row['precio'],
                    'categoria_id' => $category->id,
                ]);
                $this->updated++;

                continue;
            }

            $product = Product::create([
                'nombre' => trim((string) $row['nombre']),
                'descripcion' => $row['descripcion'] ?? null,
                'precio' => $row['precio'],
                'stock' => (int) ($row['stock'] ?? 0),
                'categoria_id' => $category->id,
                'estado' => true,
            ]);

            // El stock del archivo entra al kardex como «inventario inicial»
            $this->inventory->opening($product);
            $this->created++;
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'categoria' => ['required', Rule::exists('categorias', 'nombre')->whereNull('deleted_at')],
        ];
    }

    // Mensajes en español (la aplicación usa el idioma por defecto de Laravel: inglés)
    public function customValidationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.exists' => 'La categoría «:input» no existe. Créela primero o corrija el nombre.',
        ];
    }
}
