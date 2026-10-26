# Guion Técnico Maestro de Sustentación (10 Minutos) — 100% Código, Arquitectura y Base de Datos
## QuickFood ERP &bull; Plataforma Empresarial para Dark Kitchen
### Seminario Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD &bull; COTECNOVA 2026
### Evaluación: Entregable Segundo Corte — Avance del Proyecto (Video + Documento)

---

## 👥 Reparto, Roles y Datos de los Expositores
* **Asignatura:** Software de Gestión Empresarial
* **Docente Evaluador:** Comité Académico COTECNOVA
* **Expositor 1 (Estudiante 1):** **Miguel Ángel Ramírez Gutiérrez**  
  *Especialidad en la exposición:* Arquitectura de Controladores (`SaleController`), Modelos Eloquent (`Sale`, `Product`, `Client`), Restricciones de Migraciones en MySQL, Bloqueo Pesimista (`lockForUpdate`), Rollback Transaccional y Reglas de Negocio en InnoDB.
* **Expositor 2 (Estudiante 2):** **Andrés Felipe Mejía Suaza**  
  *Especialidad en la exposición:* Capa de Servicios (`SaleService`), Validación Form Requests (`StoreSaleRequest`), Matriz RBAC Spatie (`RolePermissionSeeder`), Middlewares de Autorización (`HasMiddleware`), Excepciones HTTP 403 (`UnauthorizedException`), Agregaciones Eloquent en Dashboard y Batería de Pruebas Automatizadas (PHPUnit).
* **Duración Estricta:** **10:00 minutos exactos** *(Nota de rúbrica: lo que supere el minuto 10 no es evaluado)*.
* **Directriz Pedagógica:** **Exclusividad de Código y Lógica Backend.** La exposición prescinde de descripciones visuales superficiales (no se habla de colores, clics, menús ni punteros) y se fundamenta estrictamente en clases PHP, closures, transacciones atómicas, consultas SQL, excepciones y directivas de seguridad.

---

## 🛠️ Checklist del Entorno de Desarrollo (Antes de Iniciar la Grabación)
Tener abiertas y configuradas las siguientes herramientas en pantalla:

1. **Editor de Código (Visual Studio Code):**
   * Zoom aumentado con `Ctrl + +` (fuente legible 18px en video).
   * Pestaña 1: `app/Services/SaleService.php` (métodos `register()` y `nextInvoiceNumber()`).
   * Pestaña 2: `app/Http/Controllers/SaleController.php` (implementación de `HasMiddleware`, `store()` y `destroy()`).
   * Pestaña 3: `app/Http/Requests/StoreSaleRequest.php` (método `rules()`).
   * Pestaña 4: `database/seeders/RolePermissionSeeder.php` (configuración Spatie y roles).
   * Pestaña 5: `app/Http/Controllers/DashboardController.php` (consultas de agregación SQL).
2. **Terminal de Base de Datos (MySQL / Sail):**
   * Conectada al motor: `./vendor/bin/sail mysql -u root -p`
   * Base de datos activa: `USE quickfood_erp;`
   * Consulta preparada: `SELECT id, nombre, stock FROM productos WHERE id = 1;`
3. **Terminal de Comandos de Laravel (PHPUnit):**
   * Lista en el directorio raíz para ejecutar: `php artisan test`
4. **Navegador Web (Para invocar endpoints y disparar middlewares):**
   * Pestañas abiertas en: `http://localhost:8090/sales/create`, `http://localhost:8090/sales`, `http://localhost:8090/dashboard`.
5. **Cronómetro:** Celular visible para monitorear el cumplimiento estricto de cada minuto.

---

## ⏱️ Matriz de Distribución de Tiempos y Componentes de Código

| Minuto | Bloque Temático (Syllabus) | Expositor | Clases, Métodos y Componentes de Código Fuente |
| :---: | :--- | :---: | :--- |
| **0:00 – 1:00** | **1. Presentación y Arquitectura del Backend** | Miguel (0:00-0:30)<br>Andrés (0:30-1:00) | Laravel 11, RAD, patrón Service Layer (`SaleService`), aislamiento MVC, concurrencia en Dark Kitchen. |
| **1:00 – 2:30** | **2. Modelo de Datos y Reglas de Negocio en Código** | Miguel (1:00-1:45)<br>Andrés (1:45-2:30) | Migraciones `create_sales_table`, `create_sale_details_table`, claves foráneas con `constrained()->onDelete('restrict')`, trait `SoftDeletes`, `DB::transaction()` y `lockForUpdate()`. |
| **2:30 – 6:30** | **3. Demostración Funcional desde el Código y MySQL** | Miguel (2:30-4:30)<br>Andrés (4:30-6:30) | `StoreSaleRequest`, `Rule::exists()`, `$service->register()`, `decrement('stock')`, verificación SQL en InnoDB, `ValidationException` con Rollback atómico, anulación con `increment('stock')`, vista con `@media print` y agregaciones en `DashboardController`. |
| **6:30 – 8:30** | **4. Seguridad, Roles Spatie y Middlewares** | Miguel (6:30-7:30)<br>Andrés (7:30-8:30) | `RolePermissionSeeder.php`, `PermissionRegistrar::forgetCachedPermissions()`, `HasMiddleware`, directivas Blade `@can`, intercepción y excepción `UnauthorizedException` (HTTP 403 Forbidden). |
| **8:30 – 9:30** | **5. Inmersión Técnica en Código Transaccional (`SaleService`)** | Miguel (8:30-9:30) | Análisis línea a línea en VS Code de `DB::transaction()`, propiedades ACID y prevención de condiciones de carrera con Row-Level Locking (`FOR UPDATE`). |
| **9:30 – 10:00**| **6. Pruebas Automatizadas y Plan Tercer Corte** | Andrés (9:30-10:00) | Ejecución en vivo de `php artisan test` (22 tests en verde) y roadmap tecnológico: Observers, Sanctum REST API y Queues. |

---

# 🎬 GUION MAESTRO PASO A PASO (100% CÓDIGO Y ARQUITECTURA)

---

### BLOQUE 1: Presentación, Empresa y Arquitectura del Backend
**Tiempo:** 0:00 – 1:00 (60 segundos)

* **💻 Qué se muestra en pantalla:** Entorno de VS Code con el árbol de directorios abierto mostrando `app/Http/Controllers`, `app/Services`, `app/Models`, `routes/web.php` y el archivo `composer.json` (Laravel 11).

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (0:00 – 0:30):
> *"Buenas noches docente y evaluadores. Mi nombre es **Miguel Ángel Ramírez Gutiérrez**, y junto a mi compañero **Andrés Felipe Mejía Suaza**, sustentamos el Segundo Corte de la asignatura **Software de Gestión Empresarial**, dentro del Seminario de Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD de **COTECNOVA (2026)**.*  
> *Presentamos la arquitectura de software de **QuickFood ERP**, una plataforma diseñada para gobernar las operaciones transaccionales y de abastecimiento de una empresa gastronómica bajo el modelo **Dark Kitchen (Cocina Oculta)** con servicio exclusivo a domicilio.*  
> *El sistema está construido sobre **Laravel 11**, adoptando una arquitectura desacoplada que separa estrictamente la capa de presentación, la capa de controladores RESTful, la capa de servicios transaccionales y el modelo de persistencia relacional con Eloquent."*

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (0:30 – 1:00):
> *"El núcleo del problema que resuelve este ERP en una Dark Kitchen es la **concurrencia transaccional en el inventario**. Durante horas de alta demanda, múltiples pedidos entran en paralelo, lo que ocasionaba desincronización y sobreventa de platos agotados.*  
> *Para erradicar este cuello de botella, en este segundo corte no nos limitamos a un CRUD convencional: diseñamos una capa de servicio especializada (`SaleService`) que orquesta transacciones atómicas mediante `DB::transaction()`, implementa bloqueos pesimistas en base de datos mediante `Product::lockForUpdate()`, aplica un esquema de autorización granular con **Spatie Laravel Permission** y calcula métricas analíticas en tiempo real mediante agregaciones optimizadas en MySQL."*

---

### BLOQUE 2: Modelo de Datos y Reglas de Negocio en el Código
**Tiempo:** 1:00 – 2:30 (90 segundos)

* **💻 Qué se muestra en pantalla:** Diagrama Entidad-Relación extendido (`figura01_diagrama_er_extendido.png`), seguido de las migraciones `create_sales_table.php` y `create_sale_details_table.php` en VS Code.

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (1:00 – 1:45):
* **Acción en pantalla:** Señalar en el código de la migración las líneas de claves foráneas y soft deletes:
  ```php
  $table->foreignId('client_id')->constrained()->onDelete('restrict');
  $table->softDeletes();
  ```

> *"En el código de las migraciones estructuramos el modelo entidad-relación garantizando integridad referencial estricta:*  
> *1. Creamos la tabla **`clients`** con campos de identificación tributaria y la bandera booleana `active`.*  
> *2. En **`create_sales_table`** definimos la cabecera con `$table->string('invoice_number')->unique()`, campos numéricos decimales `subtotal`, `tax` y `total` con precisión `(12, 2)`, y la clave foránea `$table->foreignId('client_id')->constrained()->onDelete('restrict')`. Esta restricción a nivel de base de datos impide que la base de datos permita eliminar un cliente si registra transacciones vinculadas.*  
> *3. En **`create_sale_details_table`** modelamos la relación uno a muchos mediante `$table->foreignId('sale_id')->constrained()->onDelete('cascade')` y `$table->foreignId('product_id')->constrained()->onDelete('restrict')`, almacenando snapshots inmutables de `quantity`, `unit_price` y `subtotal`.*  
> *4. En todos los modelos incorporamos el trait **`use SoftDeletes;`**, agregando la columna `deleted_at`. Esto asegura que las eliminaciones no sean destructivas en MySQL, protegiendo la trazabilidad contable y legal."*

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (1:45 – 2:30):
* **Acción en pantalla:** Abrir `app/Services/SaleService.php` y señalar las instrucciones de bloqueo y excepciones.

> *"Sobre este esquema de base de datos implementamos tres reglas de negocio esenciales codificadas en el backend:*  
> * **Regla 1 (Bloqueo Pesimista en Inventario):** En `SaleService.php`, cada ítem solicitado ejecuta `Product::lockForUpdate()->findOrFail($productId)`. Esto traduce una sentencia SQL `SELECT ... FOR UPDATE` en el motor InnoDB, impidiendo que otra petición web simultánea lea o modifique el registro hasta que la transacción actual termine.*  
> * **Regla 2 (Validación de Stock y Rollback Atómico):** Si `$product->stock < $quantity` o si el producto tiene su estado booleano `active` en falso, el código interrumpe el flujo arrojando una `ValidationException::withMessages()`. Esta excepción no controlada dentro del closure obliga a `DB::transaction()` a emitir un Rollback automático en la conexión PDO, garantizando que el inventario nunca caiga en valores negativos.*  
> * **Regla 3 (Restitución Atómica en Anulación):** En `SaleController@destroy`, al anularse una venta, el backend ejecuta una transacción donde itera cada detalle ejecutando `$detail->product->increment('stock', $detail->quantity)`, actualiza el estado a 'anulada' y ejecuta el borrado lógico con `$sale->delete()`."*

---

### BLOQUE 3: Demostración Funcional en Vivo desde el Código y Base de Datos
**Tiempo:** 2:30 – 6:30 (4 minutos)

* **💻 Qué se muestra en pantalla:** Terminal interactiva con consola MySQL + VS Code (`StoreSaleRequest.php` y `SaleService.php`) + Pestaña de navegación para disparar las rutas HTTP.

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (2:30 – 3:30) — [Paso 3.1: Consulta SQL Inicial y Validación Form Request]:
* **Acción en pantalla:**
  1. En la terminal de MySQL ejecutar la consulta en vivo:
     ```sql
     SELECT id, nombre, stock FROM productos WHERE id = 1;
     ```
     *(Se evidencia en la salida de consola: Stock = 50 unidades).*
  2. En VS Code, abrir `app/Http/Requests/StoreSaleRequest.php`.

> *"Iniciamos la demostración funcional consultando el motor InnoDB de MySQL por terminal:*  
> *Vemos que el producto con ID 1 tiene actualmente un valor en el campo `stock` de **50 unidades**.*  
> *Al emitir la petición HTTP con método `POST` hacia la ruta `/sales`, Laravel despacha la solicitud a través del Form Request `StoreSaleRequest`.*  
> *En su método `rules()`, observamos dos directivas críticas de validación:*  
> *Primero: `Rule::exists('clients', 'id')->whereNull('deleted_at')->where('active', true)`, asegurando que el cliente exista en la tabla, esté activo y no esté marcado como eliminado en `deleted_at`.*  
> *Segundo: valida la matriz de detalles con las reglas `items.*.product_id` y `items.*.quantity`, obligando a que cada ítem sea un entero mayor o igual a 1. Si la petición no cumple estas reglas, el ciclo se cancela antes de tocar la base de datos."*

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (3:30 – 4:30) — [Paso 3.2: Transacción, Descuento Atómico y Verificación SQL]:
* **Acción en pantalla:**
  1. Enviar una petición de venta con **2 unidades** del producto 1 (Hamburguesa Monster Doble).
  2. Volver inmediatamente a la terminal de MySQL y reejecutar la consulta:
     ```sql
     SELECT id, nombre, stock FROM productos WHERE id = 1;
     ```
     *(Se evidencia en la salida de consola: Stock = 48 unidades).*
  3. En VS Code señalar el cálculo de impuestos en `SaleService.php`.

> *"Al superar la validación del Request, `SaleController@store` transfiere el control a `SaleService::register()`.*  
> *Observemos la lógica del servicio:*  
> *Aplica la fórmula impositiva `$tax = round($subtotal * 0.19, 2)`, calcula el gran total y genera el consecutivo de factura invocando `nextInvoiceNumber()`, el cual consulta `Sale::withTrashed()->orderByDesc('id')->first()` y le aplica la función nativa `str_pad` para obtener `'FV-00001'`.*  
> *Seguidamente, dentro de la misma transacción, ejecuta la sentencia atómica `$item['product']->decrement('stock', $item['quantity'])`.*  
> *Si regresamos a la consola de MySQL y ejecutamos nuevamente la consulta `SELECT`, comprobamos que el motor de persistencia redujo el stock de forma atómica de 50 a **48 unidades**, demostrando la integridad de la transacción."*

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (4:30 – 5:30) — [Paso 3.3: Disparo de Excepción por Sobreventa y Rollback en BD]:
* **Acción en pantalla:**
  1. Intentar registrar una venta enviando en la carga útil una cantidad de **999 unidades**.
  2. El sistema rechaza la operación y retorna con el mensaje: *"Stock insuficiente para «Hamburguesa Monster Doble». Disponible: 48, solicitado: 999."*
  3. Ejecutar nuevamente el SELECT en la consola de MySQL:
     ```sql
     SELECT id, nombre, stock FROM productos WHERE id = 1;
     ```
     *(Se confirma que el stock se mantiene intacto en 48 unidades).*

> *"Ahora testeamos en tiempo real el mecanismo de protección ante sobreventas: enviamos un payload solicitando 999 unidades, una cifra que excede las 48 unidades disponibles.*  
> *En el código de `SaleService`, la condición de guarda `if ($product->stock < $quantity)` se evalúa como verdadera y ejecuta:*  
> *`throw ValidationException::withMessages(['items' => 'Stock insuficiente...']);`*  
> *Al propagarse esta excepción dentro del closure de `DB::transaction()`, Laravel intercepta el error y emite automáticamente un `PDO::rollBack()`.*  
> *Comprobamos en la terminal de MySQL ejecutando el `SELECT` que el campo `stock` se preserva exactamente en **48 unidades**. No se generaron registros huérfanos en `sales` ni en `sale_details`, certificando la propiedad de **Atomicidad**."*

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (5:30 – 6:30) — [Paso 3.4: Anulación con Increment, Comprobante Print y Dashboard Eloquent]:
* **Acción en pantalla:**
  1. Mostrar la vista de factura generada `sales/show.blade.php` con su estructura `@media print` para exportación a PDF.
  2. Ejecutar la acción de anulación (`DELETE /sales/{id}`).
  3. Ejecutar de nuevo el SELECT en MySQL:
     ```sql
     SELECT id, nombre, stock FROM productos WHERE id = 1;
     ```
     *(El stock se restituye exactamente a 50 unidades).*
  4. En VS Code, abrir `app/Http/Controllers/DashboardController.php`.

> *"En `sales/show.blade.php`, la vista carga las relaciones mediante `$sale->load(['client', 'user', 'details.product'])`, e implementa reglas CSS `@media print` que formatean la factura electrónica para su exportación a documento PDF sin dependencias externas.*  
> *Al ejecutar la anulación de la factura, la petición invoca `SaleController@destroy`.*  
> *El controlador encapsula la reversión en un `DB::transaction()`, ejecutando `$detail->product->increment('stock', $detail->quantity)`, actualizando el campo `status` a 'anulada' y aplicando el soft delete con `$sale->delete()`.*  
> *En la consola de MySQL verificamos que el stock fue reintegrado exactamente a sus **50 unidades iniciales**.*  
> *Finalmente, en `DashboardController@index`, observamos cómo las métricas se generan mediante consultas de agregación en base de datos: el valor del inventario ejecuta `Product::where('active', true)->sum(DB::raw('price * stock'))`, y el Top 5 ejecuta `SaleDetail::select('product_id', DB::raw('SUM(quantity) as total_sold'))->groupBy('product_id')->orderByDesc('total_sold')->limit(5)`."*

---

### BLOQUE 4: Seguridad, Roles Spatie y Middlewares en el Backend
**Tiempo:** 6:30 – 8:30 (2 minutos)

* **💻 Qué se muestra en pantalla:** Archivos `database/seeders/RolePermissionSeeder.php`, `SaleController.php` (método `middleware()`), directivas `@can` en `layouts/app.blade.php`, y la pantalla del navegador arrojando el error **HTTP 403 Forbidden**.

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (6:30 – 7:30):
* **Acción en pantalla:** En VS Code, abrir `RolePermissionSeeder.php` y mostrar `SaleController.php`.

> *"La capa de autorización está gobernada por el paquete `spatie/laravel-permission`.*  
> *En `RolePermissionSeeder.php`, la instrucción `app(PermissionRegistrar::class)->forgetCachedPermissions()` purga la memoria caché de Spatie.*  
> *Definimos los roles de negocio `admin`, `vendedor` y `almacenista`, asociando permisos modulares mediante la función `$role->syncPermissions(...)`.*  
> *En la capa de controladores, implementamos la interfaz nativa de Laravel 11 `HasMiddleware`. En `SaleController.php`, configuramos el método estático `middleware()`:*  
> *`new Middleware('permission:ver-ventas', only: ['index', 'show'])`*  
> *`new Middleware('permission:crear-ventas', only: ['create', 'store'])`*  
> *`new Middleware('permission:eliminar-ventas', only: ['destroy'])`.*  
> *Esto garantiza que la seguridad no dependa del frontend: el kernel HTTP intercepta cualquier solicitud ilegítima antes de alcanzar el método del controlador."*

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (7:30 – 8:30):
* **Acción en pantalla:**
  1. En VS Code, mostrar las directivas `@can('ver-ventas')` en `resources/views/layouts/app.blade.php`.
  2. En el navegador, autenticarse con el usuario `almacenista@test.com` mediante `quickLogin()`.
  3. En la barra de direcciones del navegador, intentar ingresar manualmente a: `http://localhost:8090/sales`.
  4. El servidor aborta la petición arrojando la pantalla oficial de Laravel: **403 | USER DOES NOT HAVE THE RIGHT PERMISSIONS**.
  5. En VS Code, mostrar el método `restore()` en `ProductoController.php`: `Product::withTrashed()->findOrFail($id)->restore()`.

> *"En la capa de interfaz, Blade utiliza la directiva condicional `@can('ver-ventas')` para no renderizar los hipervínculos a usuarios que no posean el permiso.*  
> *Para evidenciar la inviolabilidad del backend: iniciamos sesión como 'Almacenista', rol que únicamente tiene permisos sobre productos y compras. Forzamos la URL `/sales` directamente en el navegador.*  
> *De inmediato, el `PermissionMiddleware` de Spatie intercepta el ciclo de vida, evalúa que `$user->hasPermissionTo('ver-ventas')` es falso y dispara la excepción `UnauthorizedException`, retornando el código de estado **HTTP 403 Forbidden**.*  
> *Adicionalmente, el rol Administrador tiene acceso a la papelera mediante el scope `Product::onlyTrashed()`, donde la restauración invoca `$product->restore()`, ejecutando en base de datos `UPDATE productos SET deleted_at = NULL`."*

---

### BLOQUE 5: Inmersión Técnica en el Código Transaccional (`SaleService`)
**Tiempo:** 8:30 – 9:30 (1 minuto)

* **💻 Qué se muestra en pantalla:** VS Code en pantalla completa centrado en `app/Services/SaleService.php`, haciendo zoom en las líneas 18 a 85 correspondientes al método `register()`.

---

#### 🎙️ Qué dice MIGUEL ÁNGEL RAMÍREZ (8:30 – 9:30):
* **Acción en pantalla:** Seleccionar con el cursor en VS Code el bloque de `DB::transaction()` y la invocación de `lockForUpdate()`.

> *"Analizamos en detalle el corazón del backend: la clase de servicio `SaleService.php`.*  
> *El método `register()` encapsula toda la operación dentro de un closure en `DB::transaction()`. Esto garantiza el cumplimiento estricto de las 4 propiedades **ACID** de las bases de datos relacionales:*  
> * **Atomicidad:** Se crea la cabecera `Sale::create()`, se persisten los detalles mediante `$sale->details()->create()`, y se descuenta el inventario con `$product->decrement('stock')`, o se revierte la totalidad de la operación.*  
> * **Consistencia:** Las reglas de integridad de claves foráneas y la restricción de no permitir stock negativo se preservan inmutables.*  
> * **Aislamiento:** La instrucción `Product::lockForUpdate()->findOrFail($productId)` ejecuta un bloqueo pesimista a nivel de fila (`SELECT ... FOR UPDATE`). Si dos peticiones simultáneas intentan vender el mismo producto, InnoDB obliga al segundo hilo a esperar en cola hasta que el commit del primero finalice, erradicando **Race Conditions**, lecturas sucias y ventas fantasmas.*  
> * **Durabilidad:** Al completarse el closure, la transacción ejecuta el commit garantizando que los datos persistan de forma indeleble en el almacenamiento físico de la base de datos."*

---

### BLOQUE 6: Pruebas Automatizadas en Verde y Plan del Tercer Corte
**Tiempo:** 9:30 – 10:00 (30 segundos)

* **💻 Qué se muestra en pantalla:** Terminal de comandos ejecutando el comando de pruebas unitarias y de integración de Laravel.

---

#### 🎙️ Qué dice ANDRÉS FELIPE MEJÍA (9:30 – 10:00):
* **Acción en pantalla:** Ejecutar en la consola:
  ```bash
  php artisan test
  ```
  *(Se muestran en pantalla las suites `Clase9IntegracionModulosTest` y `Clase7RolesPermisosTest` finalizando con: `PASS` y `OK (22 tests, 58 assertions)` en color verde).*

> *"Para certificar que toda la lógica expuesta es tolerante a fallos, implementamos una batería de pruebas automatizadas con PHPUnit:*  
> *Como observamos en la consola, la ejecución de `php artisan test` arroja un resultado del **100% de tests en verde**: validamos transacciones completas con `assertDatabaseHas`, bloqueos de stock insuficiente con `expectException(ValidationException::class)`, respuestas HTTP 403 ante acceso no autorizado y agregaciones de KPIs.*  
> *Para el Tercer Corte implementaremos:*  
> *1. Módulo de auditoría contable basado en Eloquent Observers.*  
> *2. Una API RESTful securizada con Laravel Sanctum para la sincronización con los repartidores móviles.*  
> *3. Colas de trabajo asíncronas con Laravel Queues para el envío de facturas electrónicas por correo.*  
> *Con esto demostramos que QuickFood ERP cumple todos los requerimientos técnicos y arquitectónicos del Segundo Corte. Muchas gracias."*

---

## 📋 Resumen Rápido de Comandos para los Expositores

```bash
# 1. En la pestaña de MySQL (para consultar y evidenciar el stock en el motor InnoDB):
./vendor/bin/sail mysql -u root -p
USE quickfood_erp;
SELECT id, nombre, stock FROM productos WHERE id = 1;

# 2. En la pestaña de terminal de Laravel (para el cierre con las pruebas en verde):
php artisan test

# 3. Comprobar que los contenedores de Docker estén activos:
./vendor/bin/sail ps
```

---
*Guion técnico oficial compilado y validado según los criterios de evaluación de COTECNOVA (Seminario RAD Laravel 2026).*  
*Autores del Proyecto: Miguel Ángel Ramírez Gutiérrez & Andrés Felipe Mejía Suaza.*
