# Directorio de Entrega — Segundo Corte
## Asignatura: Software de Gestión Empresarial (SGE) &bull; COTECNOVA 2026
**Proyecto:** QuickFood ERP (Dark Kitchen)  
**Estudiantes:** Miguel Ángel Ramírez Gutiérrez & Andrés Felipe Mejía Suaza  
**Repositorio GitHub:** [https://github.com/NiquelAwol/Dark-Kitchen.git](https://github.com/NiquelAwol/Dark-Kitchen.git)  
**Etiqueta de Entrega:** `avance-2`

---

### Contenido de esta Carpeta (`docs/avance2/`):

1. **[Documento Oficial de Avance (Markdown Completo)](Avance2_QuickFood_Ramirez_Mejia.md):**
   * Contiene las 11 secciones obligatorias de la guía (Portada, Resumen, Análisis actualizado, MER extendido, Diccionario de datos, 10 Reglas de negocio, Matriz de seguridad Spatie con rol activo y permiso directo, Integración de procesos, Dashboard y análisis de KPIs, Evidencias numeradas, Dificultades/Soluciones y Plan para el tercer corte).

2. **[Carpeta de Evidencias Gráficas (`capturas/`)](capturas/):**
   * `figura01_diagrama_er_extendido.png`: Diagrama Entidad-Relación extendido.
   * `figura02_estructura_mysql_tablas.png`: Estructura en MySQL de `clients`, `sales` y `sale_details`.
   * `figura03_codigo_servicio_transaccional.png`: Código del servicio `SaleService` con `DB::transaction` y `lockForUpdate()`.
   * `figura04_formulario_ventas_reactivo.png`: Formulario dinámico de ventas con cálculo de IVA en tiempo real.
   * `figura05_factura_venta_imprimible.png`: Comprobante de venta estructurado con desglose de ítems e IVA 19%.
   * `figura06_anulacion_y_reversion_stock.png`: Auditoría en terminal MySQL demostrando reposición automática de stock tras anulación.
   * `figura07_dashboard_kpis_top5.png`: Panel de control con 5 KPIs y ranking Top 5 de productos más vendidos.
   * `figura08_seguridad_roles_spatie_403.png`: Conmutador de roles y pantalla de error HTTP 403 Forbidden.
   * `figura09_papelera_restauracion_softdeletes.png`: Vista de papelera y verificación de `deleted_at`.
   * `figura10_tests_automatizados_phpunit.png`: Ejecución de la suite completa con 22 pruebas aprobadas (100% verde).
