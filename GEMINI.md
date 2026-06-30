# TERRANOVA - Documentación del Proyecto

## Stack Tecnológico
- **Framework:** Laravel 9 (PHP 8.0+)
- **Base de datos:** PostgreSQL (motor principal)
- **Frontend:** Bootstrap 5, jQuery, DataTables, Select2, SweetAlert2, Cropper.js
- **Template Admin:** NobleUI v2.2 (ThemeForest)
- **Fuente personalizada:** ND LOGOS REGULAR (branding TERRANOVA)

---

## Variables de Entorno (`.env`)
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=terranova
DB_USERNAME=postgres
DB_PASSWORD=Garrobo1995
```

---

## Estructura de Directorios
```
app/Http/Controllers/
  AuthController.php           -- Login/logout
  DashboardController.php      -- Estadísticas del dashboard global
  Clientes/
    ClientesController.php     -- CRUD clientes + datatables + perfil
  Estadisticas/
    EstadisticaController.php  -- Estadísticas por residencial (30/06/2026)
  Residenciales/
    ResidencialesController.php -- CRUD residenciales, bloques, lotes
  Ventas/
    VentasController.php       -- CRUD ventas, vender, detalle, pagos

resources/views/
  layout/
    master.blade.php            -- Layout principal (sidebar + header + footer)
    master2.blade.php           -- Layout full-page (login)
    sidebar.blade.php           -- Navegación lateral con branding TERRANOVA
    header.blade.php            -- Barra superior con avatar y dropdown
    footer.blade.php            -- Pie de página
  pages/auth/
    login.blade.php             -- Pantalla de inicio de sesión
  terranova/
    clientes/
      clientes.blade.php        -- Listado con DataTable server-side
      perfil.blade.php          -- Perfil detallado del cliente
    residenciales/
      residenciales.blade.php   -- CRUD residenciales
      bloques.blade.php         -- CRUD bloques
      lotes.blade.php           -- CRUD lotes
    ventas/
      ventas.blade.php          -- Listado de ventas (pestañas pendientes/pagadas)
      vender.blade.php          -- Procesar nueva venta (select2 con AJAX)
      detalle.blade.php         -- Detalle de venta + plan de pagos DataTable
      carrito.blade.php         -- Componente carrito (selección de lotes)
    estadisticas/
      estadistica.blade.php     -- Overview de todos los residenciales con stats
      detalle.blade.php         -- Estadísticas detalladas por residencial

storage/
  lotificadora_scripts_postgres.sql   -- Esquema completo de base de datos
  lotificadora_scripts_mysql.sql      -- Esquema alternativo MySQL
  test_data.sql                       -- Datos masivos de prueba (~150k registros)

routes/web.php                 -- Todas las rutas de la aplicación
public/css/app.css             -- CSS compilado con variables Insignia
```

---

## Esquema de Base de Datos

### Tablas del Sistema
| Tabla | Propósito |
|-------|-----------|
| `USERS` | Usuarios del sistema (admin + operadores) |
| `RESIDENCIALES` | Proyectos residenciales, FK a USERS |
| `BLOQUES` | Definiciones de bloques (A, B, C, D, E...) |
| `BLOQUES_RESIDENCIALES` | Relación muchos-a-muchos bloques ↔ residenciales |
| `LOTES` | Lotes individuales con área, linderos, precio, financiamiento |
| `CLIENTES` | Clientes con info personal, multi-tenant por ID_USER |
| `REFERENCIAS` | Referencias personales de clientes (soft delete) |
| `BENEFICIARIOS` | Beneficiarios de clientes (soft delete) |
| `CATALOGO_TIPO_PAGO` | Catálogo: 'Contado', 'Financiado' |
| `CATALOGO_ESTADO_VENTA` | Catálogo: 'Activo', 'Pagado', 'Cancelado' |
| `VENTAS` | Ventas con términos de financiamiento |
| `LOTES_VENDIDOS` | Relación lotes ↔ ventas |
| `FECHAS_COBROS` | Calendario de pagos/cuotas |
| `LOTES_APARTADOS` | Lotes apartados por usuario |

### Catálogos (sin columna CODIGO, usan NOMBRE como key de negocio)
```sql
INSERT INTO CATALOGO_TIPO_PAGO (NOMBRE, DESCRIPCION) VALUES
  ('Contado', 'Pago único al contado'),
  ('Financiado', 'Pago financiado en cuotas');

INSERT INTO CATALOGO_ESTADO_VENTA (NOMBRE, DESCRIPCION) VALUES
  ('Activo', 'Venta activa con pagos pendientes'),
  ('Pagado', 'Venta pagada en su totalidad'),
  ('Cancelado', 'Venta cancelada');
```

### Notas sobre FECHAS_COBROS
- `ESTADO` fue eliminado de la tabla
- `FECHA_PAGO` es `TIMESTAMP(0) WITHOUT TIME ZONE` (nullable)
- El estado se calcula vía `CASE WHEN` en las queries
- Cuando se paga una cuota, solo se marca `FECHA_PAGO = NOW()`

---

## Sistema de Colores Insignia (Variables CSS)

```css
:root {
  --ins-azul: #3f5981;           /* Primary */
  --ins-azul-claro: #5a7aa8;    /* Hover/light */
  --ins-azul-oscuro: #2c3f5c;   /* Dark/active */
  --ins-negro: #323232;          /* Text/brand */
  --ins-negro-claro: #4a4a4a;
  --ins-gris: #555555;
  --ins-blanco: #e6e6e6;         /* Card backgrounds */
  --ins-blanco-humo: #f2f2f2;
  --bs-primary: var(--ins-azul);
  --bs-primary-rgb: 63, 89, 129;
}
```

Reemplazos realizados sobre el CSS original de NobleUI:
- `#6571ff` → `#3f5981` en `app.css` (100+ ocurrencias)
- `101,113,255` → `63,89,129` en `app.css`

---

## Branding TERRANOVA

### Sidebar (sidebar.blade.php)
```html
<a href="#" class="sidebar-brand" style="...">
  <img src="terranova.png" alt="Logo" style="height: 18px;">
  <span><span style="color: #323232;">TERRA</span><span style="color: #3f5981;">NOVA</span></span>
</a>
```
- Fuente: `'ND LOGOS REGULAR', sans-serif`
- TERRA: negro (#323232), NOVA: azul (#3f5981)
- Sidebar header: fondo blanco, texto negro
- Sidebar body: fondo azul, links blancos
- Hover/active: fondo blanco, texto azul
- Items: Inicio, Residenciales, Clientes, Ventas, **Estadísticas**, Vender

### Login (login.blade.php)
- Mismo split TERRA/NOVA con ND LOGOS REGULAR
- `-webkit-text-stroke: 0.6px currentColor` + `text-shadow` para grosor
- Fondo gradiente (azul-oscuro → azul → azul-claro)
- Card con sombra, lado izquierdo imagen de poster

### Favicon
```html
<link rel="icon" type="image/png" href="{{ asset('/assets/images/terranova_logo.png') }}">
```

---

## Rutas (`routes/web.php`)

### Autenticación
| Método | Ruta | Controlador |
|--------|------|-------------|
| GET | `/login` | AuthController@showLoginForm |
| POST | `/login` | AuthController@login |
| GET | `/logout` | AuthController@logout (con auth) |

### Dashboard
| Método | Ruta | Controlador |
|--------|------|-------------|
| GET | `/` | DashboardController@index |

### Residenciales (prefix: `/residenciales`)
| Método | Ruta | Controlador |
|--------|------|-------------|
| GET | `/` | ver_residenciales |
| POST | `/guardar` | guardar_residencial |
| GET | `/{id}/bloques` | ver_bloques |
| POST | `/bloques/guardar` | guardar_bloque |
| GET | `/{idRes}/bloques/{idBlq}` | ver_lotes |
| POST | `/bloques/lotes/guardar` | guardar_lote |

### Clientes (prefix: `/clientes`)
| Método | Ruta | Controlador | Propósito |
|--------|------|-------------|-----------|
| GET | `/` | ver_clientes | Página principal (render vacío, DataTable server-side) |
| GET | `/datos` | datos_clientes | **AJAX** - DataTable server-side (paginación, búsqueda, orden) |
| GET | `/buscar` | buscar_clientes | **AJAX** - Select2 remoto para búsqueda de clientes |
| POST | `/guardar` | guardar_cliente | CRUD: crear/editar/eliminar cliente |
| POST | `/obtener-referencias` | obtener_referencias | **AJAX** - Referencias de un cliente |
| POST | `/obtener-beneficiarios` | obtener_beneficiarios | **AJAX** - Beneficiarios de un cliente |
| GET | `/perfil/{id}` | perfil_cliente | Perfil detallado del cliente |

### Ventas (prefix: `/ventas`)
| Método | Ruta | Controlador |
|--------|------|-------------|
| GET | `/` | ver_ventas |
| POST | `/guardar` | guardar_venta |
| GET | `/detalle/{id}` | ver_detalle_venta |
| POST | `/pagar-cuota` | pagar_cuota |

### Vender
| Método | Ruta | Controlador |
|--------|------|-------------|
| GET | `/vender` | VentasController@ver_vender |

### Estadísticas (30/06/2026)
| Método | Ruta | Controlador | Propósito |
|--------|------|-------------|-----------|
| GET | `/residenciales/{id_residencial}/estadisticas` | EstadisticaController@show | Estadísticas detalladas por residencial |

### Catch-all (404)
```php
Route::any('/{page?}', function(){ return View::make('pages.error.404'); })->where('page','.*');
```

---

## DataTables Implementación

### Tablas con DataTable

| Vista | Tipo | Endpoint AJAX | Columnas |
|-------|------|---------------|----------|
| clientes.blade.php | **Server-side** | `GET /clientes/datos` | ID, Imagen, Nombre, Identidad, Teléfono, Opciones |
| residenciales.blade.php | Client-side | HTML pre-renderizado | - |
| bloques.blade.php | Client-side | HTML pre-renderizado | - |
| lotes.blade.php | Client-side | HTML pre-renderizado | - |
| ventas.blade.php | Client-side (2 tablas) | HTML pre-renderizado | - |
| detalle.blade.php | Client-side | HTML pre-renderizado | Plan de pagos con cuotas |
| perfil.blade.php | Client-side | HTML pre-renderizado | Ventas del cliente |

### Configuración común
```javascript
language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
```

### Client-side DataTable (clientes.blade.php)
```javascript
$('#tbl_clientes').DataTable({
    responsive: true,
    serverSide: true,
    processing: true,
    ajax: { url: "/clientes/datos", type: "GET" },
    columns: [
        { data: 'id' },
        { data: null, render: function(row) { /* imagen con fallback */ } },
        { data: 'nombre_completo' },
        { data: 'identidad' },
        { data: 'contacto_telefonico' },
        { data: null, render: function(row) { /* botones perfil/editar/eliminar */ } }
    ],
    order: [[2, 'asc']],
    drawCallback: function() { feather.replace(); }
});
```

#### Método `datos_clientes()` en ClientesController
- Parámetros: draw, start, length, search.value, order.0.column, order.0.dir
- Búsqueda: ILIKE sobre nombre completo, identidad, teléfono
- Ordenamiento: mapeo por columna (ID, nombre, identidad, teléfono)
- Respuesta: `{ draw, recordsTotal, recordsFiltered, data }`

---

## Select2 Implementación

### Selector de Clientes en Vender (vender.blade.php)

**Modo remoto con AJAX:**
```javascript
$('#id_cliente').select2({
    templateResult: formatCliente,   // Imagen + texto en resultados
    templateSelection: formatCliente, // Imagen + texto en selección
    escapeMarkup: function(m) { return m; },
    ajax: {
        url: "/clientes/buscar",
        dataType: 'json',
        delay: 250,
        data: function(params) { return { q: params.term, page: params.page || 1 }; },
        processResults: function(data) { return { results: data.results, pagination: data.pagination }; },
        cache: true
    },
    minimumInputLength: 1
});
```

#### Método `buscar_clientes()` en ClientesController
- Parámetros: `q` (término), `page` (paginación)
- Búsqueda: ILIKE sobre nombre completo e identidad
- Retorna 15 resultados por página
- Respuesta: `{ results: [{ id, text, identidad, imagen }], pagination: { more: bool } }`

### Otros usos de Select2
- `sel_lote_buscador` en vender.blade.php (client-side, HTML pre-renderizado)
- `clientes/guardar` en lotes.blade.php y carrito.blade.php

---

## Multi-tenancy
Todas las queries filtran por `ID_USER = :id_user` usando `Auth::id()`.
Cada usuario ve solo sus propios datos (clientes, residenciales, etc).

---

## Datos de Prueba (`storage/test_data.sql`)
Script PostgreSQL que genera ~150,000 registros:
- **6 usuarios** (admin + op1-op5, todos con password `password`)
- **62 residenciales**, **26 bloques** (A-Z con UNIQUE INDEX), **310 bloques_residenciales** (5 por residencial)
- **31,000 lotes** (100 por bloque, nombres únicos: `L-{RESID_ID}-{BLOQUE}-{NUM}`)
- **6,000 clientes** (1,000 por usuario en bloques contiguos)
- **18,000 referencias** (3 por cliente), **12,000 beneficiarios** (2 por cliente)
- **3,100 ventas** (50 por residencial: 25 Contado/Pagado + 25 Crédito/Activo)
- **3,100 lotes_vendidos** (FK consistentes: mismo ID_USER que el residencial)
- **55,800 fechas_cobros** (primeras 6 cuotas pagadas, resto pendientes + atrasos)
- **500 lotes reservados** (FK a CLIENTE del mismo ID_USER que el residencial)
- **500 lotes apartados**
- **FK consistentes**: CLIENTES agrupados por usuario (`(i-1)/1000 + 1`), VENTAS usan CLIENTES del mismo usuario que el RESIDENCIAL
- **Idempotente**: usa `WHERE NOT EXISTS` + `ON CONFLICT DO NOTHING`

---

## Cambios Realizados (Historial)

### Estilización visual (Insignia)
- Creadas variables CSS `--ins-*` y clases utilitarias en `app.css`
- Todos los blades estilizados con header gradiente, cards azules, cabeceras azul oscuro
- Sidebar: fondo blanco en header, azul en cuerpo, hover/active invertido
- Login: fondo gradiente, card sombra, TERRANOVA split, responsive
- Favicon actualizado
- Fuente ND LOGOS REGULAR en sidebar y login
- Reemplazo `#6571ff` → `#3f5981` y `101,113,255` → `63,89,129`

### Funcionalidad
- Perfil de cliente (`/clientes/perfil/{id}`): cover gradiente, info personal, DataTable de ventas, referencias y beneficiarios
- Select2 de clientes con foto + identidad + custom matcher
- Validación: no permite eliminar cliente con ventas activas
- Fotos rotas usan `placeholder_user.png` como fallback

### Base de datos y backend
- Eliminada columna ESTADO de FECHAS_COBROS, estado calculado vía CASE WHEN
- Nuevas tablas catálogo: CATALOGO_TIPO_PAGO y CATALOGO_ESTADO_VENTA (sin columna CODIGO)
- VENTAS.TIPO_PAGO y VENTAS.ESTADO cambiados a INTEGER FK a catálogos
- Script `test_data.sql` con ~150k registros idempotentes

### Optimizaciones de rendimiento
- **DataTable server-side** en clientes.blade.php con endpoint `/clientes/datos`
- **Select2 remoto** en vender.blade.php con endpoint `/clientes/buscar`
- Eliminada carga masiva de clientes en HTML del formulario vender

### Dashboard
- Agregada gráfica lineal (Chart.js) de Ingresos vs Cobros últimos 12 meses (`dashboard.blade.php`)
- Datos mensuales desde `FECHAS_COBROS` agrupados por mes en `DashboardController`
- Dashboard estilizado con headers gradiente azul institucional y colores Insignia

### Módulo de Estadísticas por Residencial (30/06/2026)
- `Estadisticas/EstadisticaController.php`: Nuevo controlador con 6 queries por residencial:
  - `show($id)` — detalle completo de un residencial con métricas profesionales
  - `getResidencialMonthlyStats()` — esperado vs cobrado del mes, restante, % efectividad
  - `getResidencialTodayStats()` — cobros del día para el residencial
  - `getResidencialOverdueStats()` — atrasados del residencial
  - `getResidencialVentas()` — lista completa de ventas con cliente, lotes, total, cobrado, % progreso, acción "Ver"
  - `getResidencialChartData()` — últimos 12 meses de ingresos para gráfica
- NOTA: Contado/Pagado muestra TOTAL_COBRADO = TOTAL_PAGAR y PORCENTAJE = 100% (no usa FECHAS_COBROS)
- Vista `detalle.blade.php`: Breadcrumb `Residenciales > Estadísticas`, header con foto del residencial, 4 stat cards (cobros hoy, atrasados, eficiencia, por cobrar), resumen del residencial, barra de progreso mensual, gráfica Chart.js (línea 12 meses + dona cobrado/pendiente), tabla completa de ventas con columnas: #Cliente, Identidad, Lotes, Tipo, Estado, Total, Cobrado, % badge, barra de progreso, acción Ver
- Ruta: `GET /residenciales/{id_residencial}/estadisticas`
- Acceso: botón "Estadísticas" en la tabla de Residenciales (verde, icono `bar-chart-2`)
- Multi-tenant: basado en `RESIDENCIALES.ID_USER` (la residencial verifica pertenencia al usuario autenticado, no filtra por CLIENTES.ID_USER)

### Dashboard Redesign Profesional (30/06/2026)
- `DashboardController.php`: Reescrito con 7 queries de métricas profesionales multi-tenant:
  - `getMonthlyStats()` — esperado vs cobrado, restante, % efectividad
  - `getTodayStats()` — cobros del día (cantidad + monto)
  - `getOverdueStats()` — atrasados totales (cuentas vencidas sin pago)
  - `getUpcomingPayments()` — próximos 7 días (fecha formateada + cliente)
  - `getTopMorosos()` — top 5 morosos con ranking por cuotas atrasadas y adeudo
  - `getChartData()` — últimos 12 meses para gráfica de tendencia
  - `getConteos()` — lotes disponibles, clientes, ventas activas, ventas completadas
- `dashboard.blade.php`: Rediseño profesional con:
  - Header gradient con badges de resumen (lotes disp, clientes, activas)
  - 4 stat cards: Cobros del día, Atrasados, Eficiencia %, Por Cobrar
  - Barra de progreso mensual con meta vs recaudado + indicador de efectividad
  - Gráfica lineal (Chart.js) — tendencia ingresos vs cobros 12 meses
  - Dona (Chart.js) — distribución cobrado/pendiente con tooltips porcentuales
  - Tabla próximos cobros (7 días) con indicador de vencimiento
  - Tabla top morosos con badge de cuotas atrasadas y fecha más antigua
  - Estados vacíos con iconos Feather para tablas sin datos

### Sidebar
- Reducido `font-size` del texto TERRANOVA en sidebar (`master.blade.php` inline style)
- Cambiado de `20px` a `18px`

### Residenciales - Subida de imagen con Cropper.js
- Reemplazado sistema de drag & drop por **Cropper.js** (misma lógica que clientes)
- Recorte cuadrado (`aspectRatio: 1`), redimensionado a 300x300, comprimido JPEG 0.8
- Preview de imagen cuadrada al lado izquierdo del campo Nombre
- En la misma fila: Imagen (col-md-2) + Nombre (col-md-6) + Cantidad Bloques (col-md-4)
- Al editar: bloques se oculta, nombre expande a col-md-10
- Modal de recorte reutilizado de clientes

### Corrección de Bugs de Integridad (29/06/2026)

**Bug #1/#2 — Sobrescritura de abonos parciales en cuotas**
- `VentasController.php@290-298`: `pagar_cuota()` cambió de sobrescritura fija a acumulación explícita (`yaPagado + resto = total`)
- `VentasController.php@376`: `abonar()` acumula `yaPagado + montoOwed` en vez de asignar `cuotaMensual` fija

**Bug #3 — Validación de apartados al vender**
- `VentasController.php@170-177`: `guardar_venta()` ahora verifica `LOTES.ID_CLIENTE_RESERVAR` antes de vender; lanza excepción si está reservado

**Bug #4 — Multi-tenancy en ventas**
- `VentasController.php@132-138`: `guardar_venta()` verifica que `ID_CLIENTE` pertenezca a `Auth::id()` antes de insertar

**Bug #5 — Datos financieros fantasma en Contado**
- `vender.blade.php@298-311` y `carrito.blade.php@200-213`: Al enviar Contado se envían `0` en todos los campos financieros (`anios`, `tasa`, `prima`, `cuotas`, `intereses`, `cuota_mensual`)

**Bug #6 — Pérdida de precisión por redondeo en cuota**
- `vender.blade.php@278-285` y `carrito.blade.php@174-181`: Valores RAW (`_raw_*`) enviados al servidor en vez del texto formateado del DOM; residual de redondeo distribuido en la primera cuota

**Bug #7 — Desbordamiento en 32-bit**
- `VentasController.php@20`: `toCentavos()` cambió de `intval($entero.$decimal)` a `(int) round((float)$entero*100 + (int)$decimal)`

**Bug #8 — Índices faltantes en DB**
- `lotificadora_scripts_postgres.sql@102-106`: Agregados índices: `idx_clientes_id_user`, `idx_clientes_identidad`, `idx_lotes_nombre`, `idx_ventas_id_cliente`, `idx_fechas_cobros_id_venta`

**Bug #9 — Colisión de nombres en lotes**
- `ResidencialesController.php@602-610`: Prevalidación de nombre (`L-{N}`) único antes de insertar lotes batch vía `GENERATE_SERIES`

**Bug #10 — total_pagar inconsistente**
- Confirmado que `TOTAL_PAGAR` en DB = gran total (incluye prima). Envío JS ahora explícito: `total_contado` para Contado, `_raw_total_pagar + prima` para Financiado

---

## Correcciones de Estabilización para Demo (29/06/2026)

### Fix #1 — ID_RESIDENCIAL hardcodeado
- Auditado: no se encontró `= 1` hardcodeado explícito. Las rutas y controladores usan correctamente `$id_residencial` desde el parámetro de ruta.

### Fix #2 — Update de bloques (accion == 2)
- `ResidencialesController.php@371-388`: Implementado UPDATE real que persiste cambios de precio, área, colindancias y financiamiento en todos los lotes del bloque.

### Fix #3 — Dashboard multi-tenant + Rediseño profesional
- `DashboardController.php`: 7 queries con métricas profesionales. Todas filtran por `ID_USER` mediante JOIN a CLIENTES. Cada usuario ve solo sus propios datos.
- `dashboard.blade.php`: Rediseño completo con cards, gráficas Chart.js (línea + dona), tabla de próximos cobros y top morosos.

### Fix #4 — Validación server-side
- `VentasController.guardar_venta()`: `$request->validate()` con reglas para todos los campos: required, tipos, rangos (anios 0-50, tasa 0-100, cuotas 0-600, dia_cobro 1-28, lotes array min:1).
- `ClientesController.guardar_cliente()`: `$request->validate()` con reglas de tipo, longitud máxima (100/500), email validado, y regla simplificada para delete.

### Fix #5 — Server-side DataTable en Ventas
- `VentasController.datos_ventas()`: Nuevo endpoint `GET /ventas/datos` con paginación, búsqueda y ordenamiento server-side, filtrado por estado (Activo/Pagado) y por `ID_USER`.
- `ventas.blade.php`: Migrado de renderizado Blade completo (1MB+ HTML) a DataTable server-side con carga AJAX bajo demanda. Reducción drástica de payload inicial.
- `routes/web.php`: Nueva ruta `GET /ventas/datos`.

---

## Auditoría de Calidad — Recomendaciones

### 🔴 CRÍTICOS (Seguridad / Integridad de datos)

| # | Riesgo | Archivo | Descripción |
|---|--------|---------|-------------|
| 1 | **Alto** | `ResidencialesController.php:331` | `ID_RESIDENCIAL = 1` hardcodeado en delete de bloque. Rompe la UI para cualquier residencial con ID distinto de 1. |
| 2 | **Alto** | `ResidencialesController.php:298-300` | ✅ Resuelto: `accion == 2` ahora ejecuta UPDATE de precio, área, colindancias y financiamiento en todos los lotes del bloque. |
| 3 | **Alto** | Todos los controllers | Cero validación server-side (`$request->validate()`). Toda la validación es JS del lado cliente. (✅ Parcial: `VentasController.guardar_venta` y `ClientesController.guardar_cliente` ahora tienen validate()) |
| 4 | **Alto** | `ResidencialesController`, `ClientesController` | Updates/deletes no verifican `ID_USER`. Cualquier usuario autenticado puede modificar/eliminar datos de otros. (✅ Parcial: `VentasController.guardar_venta` ya verifica pertenencia del cliente) |
| 5 | **Alto** | `DashboardController.php` | ✅ Resuelto + Rediseño: Dashboard con 7 queries multi-tenant y vista profesional con cards, gráficas Chart.js (línea + dona), próximos cobros y top morosos (30/06). |
| 6 | **Alto** | `ClientesController.php:287-338` | SQL dinámico concatenado (`$where`) sin prepared statements completos. |
| 7 | **Medio** | Todos los controllers | `$e->getMessage()` expuesto al frontend — filtra detalles internos (tablas, constraints, etc.). |
| 8 | **Medio** | `ResidencialesController.php:115` | `Storage::delete()` con nombre de imagen del usuario sin sanitizar (path traversal). |
| 9 | **Medio** | `guardar_lote():645-658` | No hay verificación de overlap en reservas — dos usuarios pueden reservar el mismo lote. (✅ Parcial: `guardar_venta` ahora valida que lote no esté reservado antes de vender) |
| 10 | **Medio** | `guardar_bloque():285-295` | Inserción masiva con `GENERATE_SERIES` sin límite superior. |
| 11 | **Bajo** | `.env` | Contraseña de DB en texto plano en repositorio. |

### 🟠 ALTOS (Arquitectura / Mantenibilidad)

| # | Problema | Archivo | Descripción |
|---|----------|---------|-------------|
| 12 | Migraciones ausentes | `database/migrations/` | No hay migraciones para las 10+ tablas del negocio. Schema en SQL suelto. |
| 13 | Sin modelos Eloquent | `app/Models/` | Todo el código usa `DB::select()`, `DB::insert()` con SQL crudo. Sin relaciones, eventos, accessors, SoftDeletes. |
| 14 | Patrón "accion" (1-5) | Todos los controllers | CRUD en un solo método con switch numérico. Viola SRP. |
| 15 | Sin paginación server-side | `residenciales`, `bloques`, `lotes`, `ventas` | DataTables carga todo en cliente. Con miles de registros se vuelve lento. |
| 16 | JS duplicado ~80% | `residenciales`, `bloques`, `lotes` | Mismo código de DataTable, CRUD, CSRF copiado en cada vista. |
| 17 | Variables globales JS | Todas las vistas | `table`, `accion`, `id`, `rowNumber`, `btn_activo` en el `window` global. |
| 18 | Sin logging de auditoría | Todos los controllers | No hay `UPDATED_BY`/`DELETED_BY`. No se sabe quién modificó qué. |
| 19 | Sin Service/Repository layer | `app/` | Toda la lógica de negocio está en los controladores. |
| 20 | Sin Form Requests | `app/Http/Requests/` | No existen clases de validación personalizadas. |
| 21 | Sin tests | `tests/` | Solo hay ExampleTest de Laravel. Cero tests unitarios o de feature. |
| 22 | Código PostgreSQL-pegado | Todos los controllers | `RETURNING`, `GENERATE_SERIES`, `ILIKE`, `TO_CHAR`, `NOW()` — no portable. |
| 23 | Sin type hints | Todos los controllers | Parámetros y returns sin tipar (excepto `$request`). |
| 24 | `Use Session;` con mayúscula | `ClientesController.php:8` | Viola PSR-2/12 (PHP case-insensitive pero mala práctica). |

### 🟡 MEDIOS (Calidad de código / Deuda técnica)

| # | Problema | Archivo | Descripción |
|---|----------|---------|-------------|
| 25 | Código comentado | `AuthController.php:54-61` | Bloque de lógica comentada de login alternativo. |
| 26 | Indentación mixta | Todas las vistas | Tabs y espacios mezclados. |
| 27 | CSS repetido en `@push` | Todos los blades | Mismas reglas de estilo copiadas en 7+ vistas. |
| 28 | `<style>` inline | Todos los blades | Estilos embebidos en lugar de clases Bootstrap o CSS dedicado. |
| 29 | `alt` pobres en imágenes | `residenciales.blade.php` | `alt="user"`, `alt="..."` sin descripción. |
| 30 | Ruta catch-all 404 | `routes/web.php:63-65` | Puede interferir con rutas no definidas. |
| 31 | Sin named routes | `routes/web.php` | Todas las URLs con `url('/...')` en lugar de `route('name')`. |
| 32 | Sin archivos JS dedicados | `resources/` | Todo el JS está inline en los blades. |
| 33 | `$request->id` sin null check | Todos los controllers | Update/delete sin validar que `$id` exista. |
| 34 | N+1 query | `ver_lotes():487-489` | Consulta separada para `$residencial` cuando ya está en `$bloque`. |
| 35 | Sin índice en FECHAS_COBROS | BD | ✅ Resuelto: `idx_fechas_cobros_id_venta` agregado al schema SQL. |
| 36 | `DELETED_AT` no verificado | `ver_lotes():471` | LEFT JOIN con CLIENTES no filtra por `C.DELETED_AT IS NULL`. |
| 37 | `app copy.css` sobrante | `public/css/app copy.css` | Archivo duplicado que debe eliminarse. |
| 38 | `TO_CHAR` con locale | `ResidencialesController.php:448,468,691` | `'L999,999,999.99'` — el símbolo de moneda depende del locale. |
| 39 | Mezcla de `?` y `:param` | `VentasController.php` | Usa tanto placeholders posicionales como nombrados. |
| 40 | Sin capa de transacciones anidadas | Todos los controllers | `DB::beginTransaction()` sin manejo de rollback en todos los caminos. |
| 41 | Sin límite de longitud en inputs | Todas las vistas | Inputs sin `maxlength`, pueden enviarse strings enormes. |
| 42 | Sin `trim()` en inputs de texto | `ClientesController.php` | Los espacios al inicio/final se guardan en DB. |

### 🟢 BAJOS (Cosméticos / Mejoras menores)

| # | Mejora | Descripción |
|---|--------|-------------|
| 43 | ✅ Dashboard con filtro multi-tenant + métricas profesionales | Resuelto: DashboardController con 7 queries multi-tenant + vista rediseñada con cards, gráficas y tablas. |
| 44 | Sin exportación PDF/Excel | No hay botón para exportar clientes, ventas o reportes. |
| 45 | Sin notificaciones de cobro | No alerta sobre fechas de cobro próximas o vencidas. |
| 46 | Sin historial de pagos completo | Solo se ve el estado actual, no el histórico de movimientos. |
| 47 | Sin reportes financieros | No hay reportes de ingresos, morosidad, proyecciones. |
| 48 | Sin módulo de usuarios/roles | Solo hay admin y operadores sin diferenciación de permisos. |
| 49 | Sin recuperación de contraseña | No hay flujo de "olvidé mi contraseña". |
| 50 | Sin caché de consultas | Las consultas pesadas (dashboard, listados) no están cacheadas. |
| 51 | Sin API externa | No hay endpoints REST para integración con otros sistemas. |
| 52 | Sin CI/CD | No hay GitHub Actions ni configuración de despliegue automatizado. |
| 53 | Sin documentación de API | No hay Postman/Swagger para consumo externo. |
| 54 | Sin i18n completo | Solo español, sin soporte multi-idioma. |
| 55 | Sin breadcrumbs dinámicos | Los breadcrumbs están hardcodeados en cada vista. |
| 56 | Sin loading states en AJAX | Las peticiones AJAX no muestran indicador de carga. |
| 57 | Sin meta tags SEO | Las páginas no tienen meta description/keywords dinámicas. |
| 58 | Sin manejo de errores 500 personalizado | Solo existe 404, no hay vista para 500. |
| 59 | Sin CSRF en todas las rutas POST | Algunas rutas podrían no estar protegidas. |
| 60 | Sin rate limiting | No hay límite de peticiones para prevenir abusos. |

---

## Plan de Acción Sugerido

### Fase 1 — Inmediata (Semana 1-2)
1. ~~Corregir `ID_RESIDENCIAL = 1` hardcodeado~~ → Auditado: no hay hardcode (✅)
2. ~~Implementar update real de bloques (`accion == 2`)~~ ✅ Resuelto (29/06)
3. ~~Agregar `$request->validate()`~~ ✅ Resuelto en `guardar_venta` y `guardar_cliente` (29/06)
4. ~~Agregar `AND ID_USER = :id_user`~~ ✅ Resuelto en dashboard + guardar_venta (29/06)
5. ~~Filtrar dashboard por `Auth::id()`~~ ✅ Resuelto + rediseño profesional (30/06)
6. Reemplazar `$e->getMessage()` por logging + mensaje genérico *(pendiente)*
7. Sanitizar paths en `Storage::delete()` *(pendiente)*

### Fase 2 — Corto plazo (Mes 1-2)
8. Crear migraciones para todas las tablas
9. Implementar modelos Eloquent con relaciones y SoftDeletes
10. Refactorizar patrón "accion" en métodos individuales
11. Migrar DataTables a server-side en residenciales, bloques, lotes
12. Extraer JS común a archivos `.js` dedicados
13. Implementar logging de auditoría
14. Agregar verificación de overlap en reservas
15. Limitar `cantidad_lotes` con validación server-side
16. Agregar Form Request classes

### Fase 3 — Mediano plazo (Mes 2-4)
17. Separar Service/Repository layer
18. Escribir tests unitarios y de feature (PHPUnit)
19. Agregar paginación server-side en todas las tablas
20. Implementar caché de consultas
21. Agregar exportación PDF/Excel
22. Implementar notificaciones de cobro
23. Crear módulo de usuarios/roles y permisos
24. Implementar recuperación de contraseña

### Fase 4 — Largo plazo (Mes 4+)
25. Crear API REST con Sanctum/Passport
26. Configurar CI/CD (GitHub Actions)
27. Agregar reportes financieros y dashboard avanzado
28. Internacionalización (i18n)
29. Migrar a Livewire o Inertia.js (opcional)
30. Implementar despliegue Docker optimizado

---

## Pruebas de Carga y Estrés — 29/06/2026

### Entorno
- **Servidor:** Windows 11 Pro, Intel i7-6700 @3.4GHz (4 núcleos, 4 lógicos), 24GB RAM
- **PHP:** 8.2.4, memory_limit=512M, OPcache desactivado
- **Base de datos:** PostgreSQL 18, ~150k registros totales
- **Herramienta:** ApacheBench 2.3

### Pruebas de Carga (100 requests, 10 concurrentes)

| Endpoint | RPS | Avg (ms) | P95 (ms) | Errores |
|----------|-----|----------|----------|---------|
| Login (GET, sin auth) | 17.75/s | 520 | 631 | 3 |
| Dashboard (GET /) | 10.57/s | 887 | 1,126 | 4 |
| Clientes DataTable (GET /clientes/datos) | 12.36/s | 760 | 987 | 2 |

### Pruebas de Estrés (alta concurrencia)

| Endpoint | Requests | Concurrencia | RPS | Avg (ms) | P95 (ms) |
|----------|----------|-------------|-----|----------|----------|
| Dashboard | 50 | 25 | 9.81/s | 2,549 | 2,835 |
| Clientes DataTable | 50 | 25 | 11.90/s | 2,101 | 2,514 |
| Login (sin auth) | 200 | 50 | 17.74/s | 2,819 | 2,909 |
| Clientes Search (ILIKE) | 50 | 25 | 11.23/s | 2,225 | 2,335 |
| Ventas Page | 30 | 15 | 10.10/s | 1,486 | 1,605 |

### Análisis de Base de Datos (EXPLAIN ANALYZE)

Las queries individuales son rápidas (2–7ms), pero bajo concurrencia degradan por falta de índices en columnas de JOIN/WHERE.

**Índices existentes:** PRIMARY KEY + 5 índices agregados (29/06/2026):
- `idx_clientes_id_user ON CLIENTES(ID_USER)`
- `idx_clientes_identidad ON CLIENTES(IDENTIDAD)`
- `idx_lotes_nombre ON LOTES(NOMBRE)`
- `idx_ventas_id_cliente ON VENTAS(ID_CLIENTE)`
- `idx_fechas_cobros_id_venta ON FECHAS_COBROS(ID_VENTA)`

**Índices faltantes (aún pendientes):**
- `LOTES (ID_BLOQUE_RESIDENCIAL)`
- `LOTES (ID_CLIENTE_RESERVAR)`
- `BLOQUES_RESIDENCIALES (ID_RESIDENCIAL, ID_BLOQUE)`

### Tamaños de Tabla

| Tabla | Tamaño | Filas |
|-------|--------|-------|
| REFERENCIAS | 2,256 kB | 15,000 |
| BENEFICIARIOS | 1,928 kB | 10,000 |
| FECHAS_COBROS | 1,600 kB | 18,012 |
| CLIENTES | 912 kB | 5,000 |
| VENTAS | 208 kB | 1,001 |
| LOTES | 128 kB | 530 |

### Calificación del Proyecto: **4/10**

**Fortalezas:**
- Funcionalidad core operativa (CRUD lotes, ventas, clientes)
- Server-side DataTable en clientes (bien implementado)
- Multi-tenancy por ID_USER
- Interfaz funcional con Bootstrap + DataTables + Select2

**Debilidades:**
- ~~Sin índices en columnas de JOIN/WHERE~~ ✅ **5 índices agregados** (29/06): `idx_clientes_id_user`, `idx_clientes_identidad`, `idx_lotes_nombre`, `idx_ventas_id_cliente`, `idx_fechas_cobros_id_venta`
- **Sin tests** automatizados (unitarios, feature, ni siquiera uno)
- SQL crudo en toda la app — sin Eloquent, sin migraciones, sin modelos
- ✅ **Validación server-side agregada** en `VentasController.guardar_venta()` y `ClientesController.guardar_cliente()`
- **OPcache desactivado** — cada request recompila PHP
- ✅ **Ventas migrado a server-side DataTable** — ya no carga HTML masivo
- ~10–18 RPS bajo carga, P95 de hasta 2.8s en estrés
- Código con deuda técnica: patrón "accion" (switch numérico), JS inline, variables globales

**Potencial:** Con índices bien puestos (✅ 5 agregados, quedan 3 críticos) + OPcache → ~6/10. Con refactor a Eloquent + migraciones + tests → ~8/10.
