# Project: Terranova (Casa Aura Landing Page)

## Architecture & Framework
- **Backend:** Laravel 5.x / 6.x
- **Frontend:** NobleUI Template, Blade Templates, AJAX, DataTables
- **Database:** PostgreSQL (Migrated from MySQL)
- **Status:** Unified graphic line across all modules. Standardized headers and interactions.

## Visual Identity
- **Colors:**
  - Deep Teal (#0D4D4F)
  - Gold (#D4AF37)
  - Off-white (#F9F9F7)
- **Typography:** Montserrat (Sans-serif)
- **Motif:** Rising sun over horizon (SVG)

## Key Modules & Workflows
- **Clientes:** Full CRUD with AJAX support, consistent visual style. Now includes field validations, Toast notifications, and `btn_activo` flag to prevent double submission.
- **Lotes:** Advanced management with Reservation system. No cart concept.
- **Ventas (Nueva Venta):** Direct lote search (multi-select), real-time financial calculator. No intermediate cart.
- **Ventas (Listado/Detalle):** Payment management and tracking.
- **Dashboard:** Operational overview with unified styling.

## Recent Changes
### Clientes Module
- Fixed encoding issues in labels: `Teléfono`, `Dirección`, `Éxito`, `¿Eliminar`, `Sí`, `acción`
- Added frontend validation for required fields: Primer Nombre, Primer Apellido, Identidad, Teléfono 1, Dirección
- Added `btn_activo` flag to prevent double form submission
- Changed `Swal.fire()` to `ToastLG.fire()` for success/error notifications (consistent with residenciales module)
- Added `alertas_propias.js` dependency (defines `Toast` and `ToastLG`)

### Sidebar
- Logo image and "TERRANOVA" text now aligned on same line using `display: flex; align-items: center;`

## Conventions
- Use AJAX for CRUD operations whenever possible for a seamless experience.
- Adhere to NobleUI standards for all UI components.
- Maintain the unified graphic line (Teal/Gold/Off-white).
- Sidebar must reflect the "Nueva Venta" workflow.
- Use `ToastLG.fire()` for operation success/error feedback, `Toast.fire()` for field validation errors.
- Use `btn_activo` flag to prevent double form submissions.
