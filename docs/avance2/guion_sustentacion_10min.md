# Guion de Sustentación — Avance del Segundo Corte (Máximo 10 Minutos)
## QuickFood ERP &bull; Sistema de Gestión Empresarial para Dark Kitchen
### COTECNOVA 2026 &bull; Software de Gestión Empresarial (Seminario RAD Laravel)

---

### 👥 Integrantes y Roles en la Presentación:
* **Persona 1:** Miguel Ángel Ramírez Gutiérrez
* **Persona 2:** Andrés Felipe Mejía Suaza

> **Recomendaciones previas antes de grabar:**
> 1. Tener el sistema levantado en el navegador (`http://localhost` o `http://localhost:8090`).
> 2. Tener una terminal abierta con MySQL (`./vendor/bin/sail mysql` o cliente de base de datos) con la fuente ampliada.
> 3. Tener VS Code abierto en `app/Services/SaleService.php` con buen zoom (Ctrl + '+').
> 4. Practicar una vez con cronómetro para asegurar que no superen los 10:00 minutos.

---

## ⏱️ Cronograma Minuto a Minuto

```
0:00 ───[ 1. PRESENTACIÓN ]────────────────── 1:00  (Miguel & Andrés)
1:00 ───[ 2. MODELO Y REGLAS ]─────────────── 2:30  (Miguel: MER | Andrés: Reglas)
2:30 ───[ 3. DEMOSTRACIÓN FUNCIONAL ]──────── 6:30  (Miguel: Venta y Error | Andrés: Anulación y KPIs)
6:30 ───[ 4. SEGURIDAD Y PERMISOS ]────────── 8:30  (Miguel: Roles y Rol Activo | Andrés: 403 y Papelera)
8:30 ───[ 5. CÓDIGO TRANSACCIONAL ]────────── 9:30  (Miguel: SaleService & lockForUpdate)
9:30 ───[ 6. CIERRE Y TERCER CORTE ]──────── 10:00  (Andrés: Conclusiones)
```

---

## 🎬 GUION DETALLADO

### BLOQUE 1: Presentación y Problemática (0:00 – 1:00)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* Página de inicio o login de QuickFood ERP con el logotipo y el título del proyecto.

---

* **MIGUEL (0:00 – 0:30):**
  > "Buenas noches profesor y compañeros. Mi nombre es Miguel Ángel Ramírez Gutiérrez, y junto a mi compañero Andrés Felipe Mejía Suaza, presentamos el avance del Segundo Corte de la asignatura Software de Gestión Empresarial en el marco del Seminario RAD Laravel 2026 en Cotecnova.  
  > Nuestro proyecto es **QuickFood ERP**, un sistema diseñado a la medida para una *Dark Kitchen* o cocina oculta de comidas rápidas artesanales con venta exclusiva a domicilio."

---

* **ANDRÉS (0:30 – 1:00):**
  > "El modelo de negocio de una Dark Kitchen maneja una alta intensidad de comandas en horas pico. Antes del ERP, QuickFood enfrentaba serios problemas: pedidos cruzados, falta de control en ruta de domiciliarios y, sobre todo, **sobreventas de platos agotados** por no tener un descuento atómico en tiempo real.  
  > En este segundo corte transformamos el sistema en un ERP transaccional completo con control de inventario, facturación formal con IVA, roles de seguridad y panel de analítica gerencial."

---

### BLOQUE 2: Modelo de Datos y Reglas de Negocio (1:00 – 2:30)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* Abrir la imagen del Diagrama MER extendido (`docs/avance2/capturas/figura01_diagrama_er_extendido.png` o el documento PDF).

---

* **MIGUEL (1:00 – 1:45):**
  > "Para soportar esta operación, evolucionamos el modelo entidad-relación del primer corte.  
  > Incorporamos la tabla `clients` para la gestión tributaria y de contacto, vinculada 1 a N con `sales` para la cabecera de las facturas, y `sale_details` para los renglones de venta vinculados a `productos`.  
  > Adicionalmente, el modelo incluye la integración con `users`, el sistema de roles y permisos de Spatie, y el modelado de `suppliers` y `purchases` para el reabastecimiento de materias primas perecederas, garantizando integridad referencial mediante claves foráneas y borrado lógico con `SoftDeletes`."

---

* **ANDRÉS (1:45 – 2:30):**
  > "En cuanto a la lógica del negocio, implementamos más de 10 reglas críticas en el backend. Destacamos tres principales:  
  > **Primera:** Cada venta descuenta stock de forma atómica y obligatoria.  
  > **Segunda:** El sistema aplica bloqueo pesimista `lockForUpdate` para evitar que dos usuarios vendan la misma porción al mismo tiempo; si no hay stock suficiente, se cancela la operación y se hace rollback total.  
  > **Tercera:** Si una factura se anula, el inventario se devuelve automáticamente a cocina, y la factura pasa a estado 'anulada' manteniendo el registro contable intacto."

---

### BLOQUE 3: Demostración Funcional en Vivo (2:30 – 6:30)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* Navegador en el ERP y terminal dividida mostrando MySQL.

---

* **MIGUEL (2:30 – 4:30):**
  > *(Muestra la terminal MySQL y luego el navegador)*  
  > "Vamos a ver el sistema en acción. Primero, consultamos en MySQL el stock del producto 'Hamburguesa Monster Doble', que actualmente cuenta con 50 unidades.  
  > *(Pasa a la pantalla `/sales/create`)*  
  > Ahora ingresamos al módulo de Ventas a facturar un pedido. Seleccionamos al cliente Carlos Morales, y agregamos 2 unidades de la Hamburguesa Monster y 2 gaseosas.  
  > Noten cómo el formulario dinámico calcula en tiempo real el Subtotal de $80.000, liquida automáticamente el IVA del 19% ($15.200) y totaliza en $95.200 COP.  
  > Al presionar 'Confirmar y Facturar Venta', la transacción se procesa. Si volvemos a la terminal de MySQL y ejecutamos el SELECT, vemos que el stock bajó exactamente a 48 unidades.  
  > *(Intenta hacer una venta con cantidad excesiva, ej: 999 unidades)*  
  > Si un vendedor intenta facturar 999 unidades, el sistema bloquea la transacción, lanza la excepción de validación y gracias al rollback de base de datos, el stock no se altera ni se genera ninguna factura huérfana."

---

* **ANDRÉS (4:30 – 6:30):**
  > *(Abre la factura generada en `sales.show`)*  
  > "Continuando con el flujo, aquí observamos la factura comercial formal con su consecutivo secuencial único FV-00001, datos del cliente, vendedor responsable y desglose tributario, lista para enviarse a imprimir con este botón.  
  > *(Demuestra la Anulación)*  
  > Ahora demostraremos la regla inversa. Supongamos que el cliente cancela el pedido por una urgencia. El usuario con permisos presiona 'Anular Venta'.  
  > El sistema cambia el estado a 'anulada', aplica SoftDelete y en segundo plano devuelve las 2 hamburguesas. Si consultamos nuevamente en MySQL: el stock volvió exactamente a 50 unidades.  
  > *(Pasa al Dashboard `/dashboard`)*  
  > Todo esto impacta en tiempo real nuestro Dashboard Gerencial. Aquí la administración visualiza los 5 KPIs: Productos Activos, Alerta de Stock Bajo menor o igual a 5 unidades, Valor Total del Inventario en dinero real, Clientes Activos y Ventas del Mes.  
  > Además, tenemos el ranking interactivo del Top 5 de productos más vendidos, lo que permite al chef programar la preparación de carnes y panes antes del turno pico."

---

### BLOQUE 4: Seguridad, Roles Spatie y Control de Acceso (6:30 – 8:30)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* Conmutador rápido de roles en `/login` o en el Dashboard, y prueba de intento de acceso restringido.

---

* **MIGUEL (6:30 – 7:30):**
  > "En el ámbito de la seguridad, implementamos el paquete oficial `spatie/laravel-permission` con 3 roles por puesto de trabajo: Administrador, Almacenista y Vendedor.  
  > Diseñamos un conmutador rápido de sesiones para sustentar los diferentes perfiles.  
  > Implementamos el concepto de **Rol Activo**, el cual garantiza que cuando un empleado multitarea inicia sesión en un turno específico, la interfaz y las autorizaciones se ajusten exclusivamente a ese rol, evitando que un vendedor modifique costos o que un almacenista altere la facturación.  
  > Además, el sistema soporta **Permisos Directos**, como autorizar temporalmente a un cajero nocturno para anular facturas mediante `givePermissionTo('anular-ventas')` sin necesidad de otorgarle privilegios de administrador."

---

* **ANDRÉS (7:30 – 8:30):**
  > *(Inicia sesión como `vendedor@test.com` e intenta ingresar a `http://localhost/categorias` o eliminar un producto)*  
  > "Para demostrar la protección por middleware en controladores, estoy en sesión como 'Carlos Vendedor'.  
  > Si intento entrar a la URL protegida de `/categorias` o presionar un botón de eliminar producto, el middleware intercepta la petición y arroja de inmediato la pantalla formal de **HTTP 403 Forbidden: Acceso Denegado**.  
  > *(Cambia a rol `admin@test.com` y abre `/productos?papelera=1`)*  
  > Ahora, con el rol Administrador, podemos acceder a la Papelera de Reciclaje. Aquí vemos los productos eliminados lógicamente con `deleted_at`. Si los necesitamos de nuevo, basta con presionar 'Restaurar' y regresan al menú sin romper la integridad de los pedidos históricos."

---

### BLOQUE 5: Explicación del Código Transaccional (8:30 – 9:30)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* VS Code con el archivo `app/Services/SaleService.php` abierto y zoom en el método `register()`.

---

* **MIGUEL (8:30 – 9:30):**
  > "En el código fuente, la lógica medular reside en `app/Services/SaleService.php`.  
  > En la línea 24 abrimos un `DB::transaction`.  
  > En la línea 32 aplicamos `Product::whereIn(...)->lockForUpdate()`, que ejecuta un bloqueo pesimista a nivel de motor InnoDB en MySQL, garantizando aislamiento ACID.  
  > Luego recorremos los ítems validando que `$product->active` sea verdadero y que `$product->stock >= $qty`. Si alguna condición no se cumple, se dispara una `ValidationException` que cancela todo y ejecuta rollback automático.  
  > En la línea 58 calculamos el IVA con `round($subtotal * 0.19, 2)`, creamos la venta con número secuencial y en la línea 72 descontamos el inventario con `$product->decrement('stock', $qty)`. Si todo sale bien, la transacción se confirma con éxito."

---

### BLOQUE 6: Cierre y Plan para el Tercer Corte (9:30 – 10:00)

#### 🖥️ QUÉ MOSTRAR EN PANTALLA:
* Portada del PDF del Avance 2 (`Avance2_QuickFood_Ramirez_Mejia.pdf`) o terminal con las pruebas en verde (`22 passed`).

---

* **ANDRÉS (9:30 – 10:00):**
  > "Para concluir, el proyecto cuenta con una suite de 22 pruebas automatizadas en PHPUnit con 257 aserciones, todas pasando al 100% en verde.  
  > Toda la documentación formal de 14 páginas está compilada en PDF bajo la etiqueta `avance-2` en nuestro repositorio oficial de GitHub.  
  > Para el Tercer Corte abordaremos el módulo completo de compras con recepciones parciales de órdenes, exportación de facturas a PDF vectorizado nativo con DomPDF, y una API RESTful autenticada con Sanctum para la app de los repartidores.  
  > Muchas gracias por su atención."

---

### 💡 Consejos para la Grabación:
1. **Quién comparte pantalla:** Uno de los dos puede compartir pantalla (o turnarse) mientras ambos tienen sus micrófonos activos.
2. **Pausas:** Hagan pausas de 1 segundo entre oraciones para que la voz se escuche clara y segura.
3. **Control del Tiempo:** Si ven que en la mitad van en el minuto 4:30, van con el ritmo perfecto para terminar en 9:45.
