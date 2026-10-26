# Guion Maestro Paso a Paso para la Grabación del Video de Sustentación (10 Minutos)
## QuickFood ERP &bull; Sistema de Gestión Empresarial para Dark Kitchen
### COTECNOVA 2026 &bull; Software de Gestión Empresarial (Seminario RAD Laravel)
### Evaluación: Entregable Segundo Corte — Avance del Proyecto (Video + Documento)

---

## 👥 Reparto y Datos de los Expositores:
* **Persona 1:** **Miguel Ángel Ramírez Gutiérrez**
* **Persona 2:** **Andrés Felipe Mejía Suaza**
* **Duración Total Máxima:** **10:00 minutos exactos** *(Nota: la rúbrica estipula que lo que pase del minuto 10 no se evalúa).*
* **Resolución de Grabación recomendada:** Mínimo 720p o 1080p (usar OBS Studio, Clipchamp, Xbox Game Bar o Loom).
* **Configuración del micrófono:** Audio nítido, sin eco; ambos expositores deben hablar.

---

## 🛠️ Checklist de Preparación Previa (Antes de presionar "Grabar")

Para que la grabación fluya sin interrupciones ni tiempos muertos, abran y dejen listas las siguientes ventanas:

1. **Navegador Web (Google Chrome o Edge):**
   * Pestaña 1: `http://localhost` (o `http://localhost:8090/dashboard` si cambiaron puerto). Con la sesión abierta.
   * Pestaña 2: `http://localhost/sales/create` (Punto de venta listo para facturar).
   * Pestaña 3: Diagrama MER extendido abierto (`docs/avance2/capturas/figura01_diagrama_er_extendido.png` o en el PDF).
2. **Terminal (Windows Terminal o WSL Ubuntu):**
   * Letra grande (Ctrl + Shift + '+' en la terminal para que se lea perfectamente en el video).
   * Conectada a MySQL mediante: `./vendor/bin/sail mysql`
   * Con la base de datos seleccionada: `USE quickfood_erp;` (o `USE laravel;`).
   * Tener a mano el comando: `SELECT id, nombre, stock FROM productos WHERE id = 1;`
3. **Editor de Código (Visual Studio Code):**
   * Archivo abierto: `app/Services/SaleService.php`.
   * Zoom de fuente aumentado (`Ctrl + '+'`) en el método `register()`.
4. **Cronómetro:**
   * Un celular con cronómetro al lado de la pantalla para monitorear los minutos en tiempo real.

---

## ⏱️ Cronograma Maestro por Bloques y Tiempos

| Minuto | Bloque Temático | Expositor que Habla | Qué se Muestra en Pantalla |
| :---: | :--- | :---: | :--- |
| **0:00 – 1:00** | **1. Portada, Integrantes y Problemática** | Miguel (0:00-0:30)<br>Andrés (0:30-1:00) | Dashboard de QuickFood ERP / Portada del Documento PDF. |
| **1:00 – 2:30** | **2. Modelo de Datos y Reglas de Negocio** | Miguel (1:00-1:45)<br>Andrés (1:45-2:30) | Diagrama Entidad-Relación extendido (Figura 1) y tabla de reglas. |
| **2:30 – 6:30** | **3. Demostración Funcional en Vivo** | Miguel (2:30-4:30)<br>Andrés (4:30-6:30) | Terminal MySQL (Stock antes) &rarr; Venta con IVA &rarr; MySQL (Stock después) &rarr; Error por sobreventa &rarr; Anulación de venta y reposición de stock &rarr; Factura &rarr; Dashboard KPIs. |
| **6:30 – 8:30** | **4. Seguridad, Roles Spatie y Permisos** | Miguel (6:30-7:30)<br>Andrés (7:30-8:30) | Conmutador de roles &rarr; Rol Activo &rarr; Intento de acceso sin permiso (HTTP 403 Forbidden) &rarr; Papelera SoftDeletes y restauración. |
| **8:30 – 9:30** | **5. Explicación de Código Transaccional** | Miguel (8:30-9:30) | VS Code en `SaleService.php` (`DB::transaction` y `lockForUpdate`). |
| **9:30 – 10:00**| **6. Cierre y Plan para el Tercer Corte** | Andrés (9:30-10:00) | Terminal con las 22 pruebas automatizadas en verde y despedida. |

---

# 🎬 GUION DETALLADO: ACCIÓN POR ACCIÓN Y PALABRA POR PALABRA

---

### BLOQUE 1: Presentación, Empresa y Problema Identificado
**Tiempo:** 0:00 – 1:00 (1 minuto)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* Iniciar la grabación mostrando la pantalla de inicio o el Dashboard de **QuickFood ERP** con el logotipo y el título institucional.
* El cursor se mantiene sereno en el centro de la pantalla.

---

#### 🎙️ Qué dice MIGUEL (0:00 – 0:30):
> *"Buenas noches profesor y compañeros. Mi nombre es **Miguel Ángel Ramírez Gutiérrez**, y junto a mi compañero **Andrés Felipe Mejía Suaza**, presentamos la sustentación del Segundo Corte para la asignatura **Software de Gestión Empresarial**, dentro del Seminario de Desarrollo de Aplicaciones Web en Laravel con Enfoque RAD de **COTECNOVA (2026)**.*  
> *Nuestro proyecto es **QuickFood ERP**, una plataforma web desarrollada a la medida para centralizar la operación integral de una empresa gastronómica bajo el modelo de **Dark Kitchen (Cocina Oculta)** con reparto exclusivo a domicilio."*

---

#### 🎙️ Qué dice ANDRÉS (0:30 – 1:00):
> *"Una Dark Kitchen maneja una altísima rotación y pedidos simultáneos en horarios pico. Antes de implementar el ERP, la empresa enfrentaba serios cuellos de botella:*  
> *1. Descoordinación de comandas tomadas en papel o WhatsApp.*  
> *2. Falta de control sobre la flota de domiciliarios.*  
> *3. Y el problema más crítico: **sobreventas de productos agotados**, ya que no existía un descuento automático de inventario con control de concurrencia.*  
> *En este segundo corte, transformamos el sistema en un ERP transaccional multiusuario con control atómico de stock, facturación estructurada con IVA del 19%, seguridad con roles y un panel analítico gerencial."*

---

### BLOQUE 2: Modelo de Datos y Reglas de Negocio
**Tiempo:** 1:00 – 2:30 (1 minuto y 30 segundos)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* Cambiar de pestaña en el navegador y mostrar en pantalla completa la imagen del **Diagrama Entidad-Relación Extendido** (`figura01_diagrama_er_extendido.png`).
* El expositor señala con el cursor las tablas `clients`, `sales`, `sale_details` y `suppliers`.

---

#### 🎙️ Qué dice MIGUEL (1:00 – 1:45):
> *"Para soportar este flujo de negocio, ampliamos significativamente el modelo entidad-relación del primer corte:*  
> *Incorporamos la tabla **`clients`** para el directorio de clientes con validación estricta de documentos tributarios como Cédula de Ciudadanía y NIT, vinculada en relación 1 a N con la tabla **`sales`**, que representa la cabecera de las facturas de venta.*  
> *A su vez, **`sales`** se desglosa en **`sale_details`**, que conecta cada ítem comercializado con la tabla **`productos`**.*  
> *El modelo integra además el esquema RBAC de Spatie con roles y permisos, y el modelado de **`suppliers`** (proveedores) y **`purchases`** (órdenes de compra) para el reabastecimiento de materias primas perecederas, garantizando integridad referencial con claves foráneas y borrado lógico mediante **`SoftDeletes`**."*

---

#### 🎙️ Qué dice ANDRÉS (1:45 – 2:30):
> *"En el backend implementamos más de 10 reglas de negocio esenciales. Destacamos tres fundamentales para la sustentación:*  
> * **Regla 1 (Descuento Atómico):** Toda venta descuenta automáticamente las cantidades de inventario al confirmarse.*  
> * **Regla 2 (Bloqueo de Concurrencia y Rollback):** Mediante bloqueo pesimista `lockForUpdate`, si dos cajeros intentan vender la última hamburguesa a la vez, el sistema protege el registro; si no hay stock suficiente, aborta la operación completa mediante rollback, impidiendo que el inventario quede en números negativos.*  
> * **Regla 3 (Reposición en Anulación):** Si se anula una factura, las unidades se devuelven de inmediato al stock físico de cocina, y el comprobante pasa a estado 'anulada' manteniendo la trazabilidad contable."*

---

### BLOQUE 3: Demostración Funcional en Vivo (El Núcleo del Video)
**Tiempo:** 2:30 – 6:30 (4 minutos)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* **Paso 3.1:** Mostrar la terminal de MySQL con fuente grande.
* **Paso 3.2:** Ejecutar la consulta del stock inicial:
  ```sql
  SELECT id, nombre, stock FROM productos WHERE id = 1;
  ```
  *(Se observa que tiene 50 unidades).*
* **Paso 3.3:** Pasar al navegador a la URL: `http://localhost/sales/create`.
* **Paso 3.4:** Seleccionar al cliente *Carlos Eduardo Morales* (CC 1088234567).
* **Paso 3.5:** En la tabla de productos, agregar **2 unidades** de *Hamburguesa Monster Doble Carne* ($28.000 c/u) y **2 unidades** de *Gaseosa Coca-Cola* ($5.000 c/u).
* **Paso 3.6:** Señalar con el mouse los totales calculados automáticamente:
  * Subtotal: **$ 66.000**
  * IVA (19%): **$ 12.540**
  * Total a Pagar: **$ 78.540 COP**
* **Paso 3.7:** Dar clic en el botón verde: **"Confirmar y Facturar Venta"**.
* **Paso 3.8:** Volver de inmediato a la terminal de MySQL y volver a consultar:
  ```sql
  SELECT id, nombre, stock FROM productos WHERE id = 1;
  ```
  *(Se observa que el stock bajó exactamente a **48 unidades**).*
* **Paso 3.9:** Volver a `http://localhost/sales/create` e intentar ingresar una venta imposible: colocar cantidad **999**. Dar clic en Facturar.
  *(Aparece en rojo el mensaje de error: "Stock insuficiente" y la venta es rechazada).*

---

#### 🎙️ Qué dice MIGUEL (2:30 – 4:30):
> *(En la terminal de MySQL)*  
> *"Comprobemos el flujo en vivo. Primero, consultamos en la base de datos MySQL el stock de nuestro producto estrella: la 'Hamburguesa Monster Doble', que actualmente cuenta con **50 unidades** en existencias.*  
> *(Pasa a la pantalla de ventas en el navegador)*  
> *Ahora nos dirigimos al módulo de Ventas. Seleccionamos al cliente Carlos Morales. Añadimos 2 unidades de la Hamburguesa Monster y 2 gaseosas.*  
> *Noten cómo el formulario dinámico calcula en tiempo real el Subtotal, liquida el 19% de IVA obligatorio según la normatividad tributaria colombiana y genera el Total definitivo.*  
> *(Presiona 'Confirmar y Facturar Venta')*  
> *Al confirmar, se procesa la transacción. Si regresamos inmediatamente a la consola de MySQL y ejecutamos nuevamente la consulta... observen que el stock bajó en tiempo real de 50 a **48 unidades**.*  
> *(Intenta vender 999 unidades)*  
> *Ahora probemos la regla de protección: si un vendedor intenta facturar 999 unidades, el backend intercepta la solicitud, lanza la excepción de validación y, gracias al rollback automático de Laravel, el inventario permanece intacto en 48 unidades sin crear facturas corruptas."*

---

#### 🖥️ Acción en Pantalla (Paso a Paso):
* **Paso 3.10:** Entrar al detalle de la venta recién creada (`sales.show`). Mostrar la factura formal con su número `FV-00001` y señalar el botón de impresión.
* **Paso 3.11:** En la misma factura, dar clic en el botón rojo **"Anular Venta"**.
* **Paso 3.12:** Confirmar la alerta de anulación.
* **Paso 3.13:** Regresar a la terminal MySQL y correr de nuevo:
  ```sql
  SELECT id, nombre, stock FROM productos WHERE id = 1;
  ```
  *(Se observa que el stock volvió exactamente a **50 unidades**).*
* **Paso 3.14:** Pasar al navegador y abrir `http://localhost/dashboard`. Mostrar los 5 KPIs y la sección del "Top 5 Productos Más Vendidos".

---

#### 🎙️ Qué dice ANDRÉS (4:30 – 6:30):
> *(En la pantalla de la Factura de Venta)*  
> *"Continuando con el flujo, aquí observamos la **Factura Comercial FV-00001**, con numeración correlativa única, datos fiscales del cliente, identificación del cajero responsable y totalización desglosada, lista para su impresión física o exportación con este botón.*  
> *(Presiona el botón rojo 'Anular Venta')*  
> *Demostremos ahora la regla de anulación: si el cliente cancela el pedido por alguna eventualidad, el supervisor presiona 'Anular Venta'.  
> El sistema marca la factura en estado 'anulada', la envía a la papelera con SoftDelete y devuelve el stock. Si verificamos en MySQL: **las 2 hamburguesas han sido devueltas y el inventario vuelve a estar en 50 unidades exactas**.*  
> *(Pasa al Dashboard de la aplicación)*  
> *Toda esta actividad alimenta en tiempo real nuestro **Dashboard Gerencial**. Aquí la administración monitorea 5 métricas clave:  
> 1. Total de Productos Activos.  
> 2. Alerta de Stock Bajo (menor o igual a 5 unidades) para compras urgentes.  
> 3. Valor Total del Inventario inmovilizado en dinero real.  
> 4. Clientes Activos.  
> 5. Y Facturación acumulada del mes.  
> Además, observamos el ranking interactivo del **Top 5 de productos más vendidos**, lo que le indica al chef qué alimentos tienen mayor rotación para preparar los ingredientes con antelación."*

---

### BLOQUE 4: Seguridad, Roles Spatie y Control de Acceso
**Tiempo:** 6:30 – 8:30 (2 minutos)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* **Paso 4.1:** Mostrar el conmutador rápido de roles en pantalla (o cerrar sesión e ir a `/login`).
* **Paso 4.2:** Iniciar sesión con el usuario `admin@test.com` (Rol: Administrador). Mostrar que tiene acceso a Categorías, Productos, Clientes, Ventas y Reportes.
* **Paso 4.3:** Cambiar de sesión al usuario `vendedor@test.com` (Rol: Vendedor).
* **Paso 4.4:** Intentar ingresar a la URL protegida: `http://localhost/categorias`.
  *(La pantalla se detiene y muestra el error formal de Laravel: **403 Forbidden | User does not have the right permissions**).*
* **Paso 4.5:** Cambiar nuevamente a `admin@test.com` e ingresar a `http://localhost/productos?papelera=1`.
* **Paso 4.6:** Mostrar un producto en la papelera y dar clic en **"Restaurar"**.

---

#### 🎙️ Qué dice MIGUEL (6:30 – 7:30):
> *"En materia de seguridad, implementamos el paquete oficial **`spatie/laravel-permission`** con tres perfiles por puesto de trabajo: Administrador, Almacenista y Vendedor.*  
> *Diseñamos un conmutador de roles para validar el control de acceso en tiempo real.*  
> *Implementamos el concepto de **Rol Activo**: en una Pyme como QuickFood, si un administrador cubre un turno de caja el fin de semana, asume el rol activo de 'vendedor', asegurando que la interfaz y las autorizaciones se ajusten exclusivamente a esa labor, evitando que un cajero modifique costos de recetas o elimine categorías.*  
> *Asimismo, el sistema soporta **Permisos Directos**, permitiendo otorgar facultades especiales temporales a un empleado —como `anular-ventas` a un cajero supervisor nocturno— mediante `givePermissionTo()`, sin necesidad de elevarlo a administrador global."*

---

#### 🎙️ Qué dice ANDRÉS (7:30 – 8:30):
> *(Muestra la pantalla de error 403 Forbidden)*  
> *"Para verificar la protección por middleware en controladores, en este momento tengo la sesión activa de 'Carlos Vendedor'.*  
> *Si intento ingresar directamente a la URL de administración de Categorías o intento eliminar un plato, el middleware intercepta la solicitud y bloquea el acceso de inmediato con una respuesta **HTTP 403 Forbidden: Acceso Denegado**.*  
> *(Pasa a la papelera con el rol Admin y restaura un producto)*  
> *Ahora, regresando a la sesión de Administrador, entramos a la **Papelera de Reciclaje**. Aquí residen los productos eliminados lógicamente con `deleted_at`. Al dar clic en 'Restaurar', el producto se recupera instantáneamente sin romper las relaciones de pedidos o facturas históricas donde ese plato fue vendido."*

---

### BLOQUE 5: Explicación del Código Transaccional
**Tiempo:** 8:30 – 9:30 (1 minuto)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* Abrir Visual Studio Code con el archivo `app/Services/SaleService.php`.
* Hacer buen zoom (Ctrl + '+') para que el texto sea perfectamente legible a 720p/1080p.
* Señalar con el cursor el método `register()`.

---

#### 🎙️ Qué dice MIGUEL (8:30 – 9:30):
> *(Con el código de `SaleService.php` en pantalla)*  
> *"En el código fuente, la lógica medular reside en **`app/Services/SaleService.php`** dentro del método `register()`.*  
> *En la **línea 24**, abrimos el bloque transaccional atómico **`DB::transaction()`**.*  
> *En la **línea 32**, aplicamos **`Product::whereIn(...)->lockForUpdate()`**, que activa un bloqueo pesimista en el motor de almacenamiento InnoDB de MySQL, garantizando aislamiento total.*  
> *Luego recorremos los ítems validando que el producto esté activo y que tenga stock suficiente; si alguna regla se incumple, se dispara una `ValidationException` que cancela todo mediante rollback.*  
> *Más abajo liquidamos el IVA con `round($subtotal * 0.19, 2)`, generamos la factura correlativa y descontamos el inventario con `$product->decrement('stock', $qty)`. Si todo es correcto, la transacción realiza el commit de forma exitosa."*

---

### BLOQUE 6: Cierre, Pruebas Automatizadas y Plan Tercer Corte
**Tiempo:** 9:30 – 10:00 (30 segundos)

#### 🖥️ Acción en Pantalla (Paso a Paso):
* Mostrar la terminal ejecutando el comando de pruebas:
  ```bash
  ./vendor/bin/sail artisan test
  ```
  *(Se ven pasar en verde las 22 pruebas: `Clase7RolesPermisosTest`, `Clase9IntegracionModulosTest`, `QuickFoodErpTest` con 257 aserciones).*
* En los últimos 5 segundos, mostrar brevemente la portada del documento PDF `Avance2_QuickFood_Ramirez_Mejia.pdf`.

---

#### 🎙️ Qué dice ANDRÉS (9:30 – 10:00):
> *(Con la terminal de pruebas en verde en pantalla)*  
> *"Para concluir, el proyecto cuenta con una batería de **22 pruebas automatizadas en PHPUnit con 257 aserciones**, todas pasando al 100% en verde.*  
> *Todo el código fuente y el documento formal de 14 páginas en PDF están sincronizados en nuestro repositorio de GitHub bajo la etiqueta oficial **`avance-2`**.*  
> *Para el Tercer Corte implementaremos el módulo avanzado de compras con recepciones parciales de materia prima, exportación de facturas a PDF nativo con DomPDF, y una API RESTful autenticada con Sanctum para la app de los repartidores.*  
> *Muchas gracias por su atención."*

---

## 🎯 Consejos Clave para Obtener la Máxima Calificación (5.0):

1. **Reparto Equitativo de Voz:** La rúbrica exige que ambos participen. En este guion, Miguel habla 4 minutos y 45 segundos, y Andrés habla 4 minutos y 45 segundos (50/50 exacto).
2. **Quién maneja el mouse:** Recomendamos que mientras uno habla, el otro maneje el mouse y las ventanas, o que quien exponga tenga el control del cursor para señalar exactamente lo que va diciendo.
3. **Control del Cronómetro:** Si al finalizar el Bloque 3 (Demostración de ventas y anulación) van en el minuto **6:15 a 6:30**, van a un ritmo perfecto para culminar entre **9:40 y 9:55**.
4. **Plataforma de Subida del Video:** Graben el video, súbanlo a YouTube en modo **"No listado"** (o a Google Drive con acceso público para quien tenga el enlace) y peguen el link en la portada de su documento PDF.
