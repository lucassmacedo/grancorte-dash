# GitHub Copilot Instructions for Gran Cortes ERP

## Project Overview
This is a Laravel 10 application for Gran Cortes, a meat wholesale/distribution company. The system integrates with Protheus ERP via SQL Server and manages clients, products, orders, logistics, and NFe (Brazilian tax documents) generation. It features multi-role authorization, real-time Livewire components, and external API integrations.

## Architecture & Key Patterns

### Database Architecture
- **Primary DB**: PostgreSQL (Laravel application data)
- **External ERP**: SQL Server "protheus" connection for Protheus ERP integration
- **Dual-model pattern**: `app/Models/` (local) vs `app/Models/System/` (ERP views)
- Use `DB::connection('protheus')` for ERP queries, regular models for app data

### Permission System (Spatie)
- Role-based with granular permissions: `admin`, `vendedor`, `cliente`, `supervisor`
- Permission format: `{resource}.{action}` (e.g., `clientes.index`, `pedidos.create`)
- Admin bypass: `Gate::before()` in `AuthServiceProvider` gives admins all permissions
- Middleware: `role:admin`, `permission:clientes.index`, `can:pedidos.edit`

### Data Synchronization
- **SyncAttack class**: Central hub for ERP data sync (`app/SyncAttack.php`)
- **Import commands**: `import:clientes`, `import:produtos`, `import:vendedores`, etc.
- **Jobs**: Background sync jobs in `app/Jobs/` for data integration
- **Logging**: `LogSyncData` model tracks all sync operations

### Livewire Components
- Located in `app/Http/Livewire/` with organized subdirectories
- Key components: `NovoPedidoCreate`, data tables for client areas
- Use Laravel Livewire Tables package for complex datatables
- Follow namespace pattern: `App\Http\Livewire\{Module}\{Component}`

## Development Workflows

### Environment Setup (Kool)
```bash
kool run setup           # First-time setup
kool run composer        # Composer commands
kool run artisan         # Artisan commands
kool run reset           # Reset database state
```

### Database Operations
```bash
php artisan migrate                    # Run migrations
php artisan create:permissions        # Generate role permissions
php artisan import:clientes           # Sync clients from ERP
php artisan import:produtos           # Sync products from ERP
```

### Testing
- PHPUnit configured for Feature/Unit tests
- Use `RefreshDatabase` trait for database-dependent tests
- Authentication testing includes client portal specific flows

## Code Conventions

### Model Patterns
- **User models**: Extended with query caching, sortable, permissions, impersonation
- **Timestamps**: Use `userstamps()` in migrations for audit trails
- **Soft deletes**: Applied where business rules require record retention
- **Query caching**: Implemented via `QueryCacheable` trait (3600s default)

### Controller Organization
- Group related actions: `ClientesController`, `ClientesProspectController`, `ClientesPendeciasController`
- Separate client-facing controllers in `app/Http/Controllers/Cliente/`
- Admin controllers use permission middleware extensively

### Route Structure
```php
// Main routes include modular files
include 'auth/clientes.php';
include 'admin/clientes.php';

// Admin routes wrapped in auth + role middleware
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    // Admin-only routes
});
```

### Form Requests
- Validation rules include business-specific constraints
- Example: `PedidoUpdateRequest` validates product codes against database
- Use array validation for dynamic product lists

## Integration Points

### Protheus ERP Integration
- Views prefixed with `VW_PDV_` contain business data
- Raw SQL queries for complex ERP operations
- Transaction management across both databases for data consistency
- Use prepared statements with proper encoding for SQL Server

### NFe (Brazilian Tax Documents)
- NFePHP library for XML generation and PDF rendering
- DANFE PDF generation with company logos
- Email sending via queued jobs (`SendEmailNfeClientesQueueJob`)
- XML validation and storage in database

### External APIs
- WhatsAPI integration for notifications
- Bugsnag for error tracking and reporting
- GeoIP for location services

## Key Business Logic

### Order Management
- Orders support multiple types: regular, cut orders, logistics
- Product pricing varies by client, location, and price lists
- Cut orders (`PedidoCorte`) batch multiple orders for delivery optimization

### Client Portal
- Separate authentication for clients accessing their data
- NFe viewing and email sending functionality
- Dashboard with financial and delivery tracking

### Logistics Integration
- Route optimization with `LogisticaRoterizacao`
- Delivery tracking and status updates
- Integration with external logistics systems

## File Locations

### Key Directories
- Models: `app/Models/` (local) + `app/Models/System/` (ERP)
- Commands: `app/Console/Commands/` (sync operations)
- Jobs: `app/Jobs/` (background processing)
- Policies: `app/Policies/` (authorization logic)
- Routes: `routes/admin/`, `routes/auth/` (modular organization)

### Configuration Files
- Database: Multi-connection setup in `config/database.php`
- Permissions: Spatie configuration in `config/permission.php`
- Kool: Development workflow in `kool.yml`

Always consider ERP integration requirements when modifying business logic, and ensure proper permission checks for role-based features.
