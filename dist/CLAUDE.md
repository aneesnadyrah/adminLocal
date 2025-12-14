# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

KITER (Koridor Utiliti Teknologi Terengganu) is a multi-tenant enterprise web application for utility project management. Built with PHP/Apache, PostgreSQL, and vanilla JavaScript, it supports multiple tenants (KUTT, KUP, KUDRAT, UCIDOS) through environment-based configuration.

**Tech Stack:**
- Backend: PHP with custom MVC-like framework
- Database: PostgreSQL (pgsql)
- Frontend: Vanilla JavaScript, HTML5, CSS3
- Web Server: Apache with mod_rewrite
- Additional: FTP integration, Telegram bot, GeoServer, PWA support

## Development Commands

### Server Setup
```bash
# Enable Apache mod_rewrite (required for routing)
sudo a2enmod rewrite
sudo systemctl restart apache2

# Run with PHP built-in server (development only)
php -S localhost:8000

# Test database connection
pg_isready -h localhost -p 5432
psql -h localhost -U postgres -d interedge
```

### No Build System
This project has no build/compilation step. Direct file serving via Apache.

### Testing
No formal testing framework is configured. Testing is manual through browser and API calls:
```bash
# Test API endpoints
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"user","password":"pass"}'
```

### Scheduled Tasks
```bash
# Permit expiry check (should run hourly)
php cron/permitExpiry.php
```

## Architecture Overview

### Request Flow

1. **Entry Point:** All requests hit [index.php](index.php) (unless routed by Apache)
2. **Apache Routing:** [.htaccess](.htaccess) handles URL rewriting:
   - View routes → `/views/*.php`
   - API routes → `/api/v1/*.php`
   - Clean URLs without .php extensions
3. **Session Check:** [index.php](index.php) validates authentication and routes to appropriate views
4. **Configuration Bootstrap:** [config/autoload.php](config/autoload.php) initializes session and globals

### Multi-Tenant System

The [config/system.php](config/system.php) class automatically selects environment based on HTTP_HOST:
- Development: `.ENVDEV`
- KUTT tenant: `.KUTTENV`
- KUP tenant: `.KUPENV`
- KUDRAT tenant: `.KUDRATENV`

Each environment file contains:
- Database credentials (host, port, database, username, password)
- FTP storage configuration
- App URL
- Telegram bot token
- Secret keys

**Critical:** Never commit real credentials in .ENV files.

### MVC-Like Structure

- **Models (Business Logic):** [config/functions/](config/functions/) - 25+ function classes containing core business logic
  - [dashboard.php](config/functions/dashboard.php) - Most complex (152K lines)
  - [survey.php](config/functions/survey.php) - Survey operations (113K lines)
  - [tasking.php](config/functions/tasking.php) - Task management (69K lines)
  - [wayleave.php](config/functions/wayleave.php), [permitting.php](config/functions/permitting.php) - Project operations
- **Views:** [views/](views/) directory - PHP templates with embedded HTML
- **Controllers:** [api/v1/](api/v1/) endpoints + [config/controller.php](config/controller.php)
- **Components:** [components/](components/) - Reusable UI elements (forms, tables, modals, wizards)

### Database Pattern

[config/DBFactory.php](config/DBFactory.php) implements singleton pattern with:
- Connection pooling
- Retry logic for network spikes
- Health checks
- Statistics tracking

All database access uses PDO with prepared statements.

### API Structure

RESTful endpoints organized in [api/v1/](api/v1/) by domain:
- [authentication/](api/v1/authentication/) - Login, register, recovery
- [projects/](api/v1/projects/) - CRUD + wayleaves, permits, sites, team
- [survey/](api/v1/survey/) - Team surveys, priorities, QR codes
- [tasks/](api/v1/tasks/) - Task management and workflows
- [reports/](api/v1/reports/) - Data export and summaries
- [geospatial/](api/v1/geospatial/) - GIS integration with GeoServer
- [letters/](api/v1/letters/) - Letter generation and routing
- [calendar/](api/v1/calendar/), [trackers/](api/v1/trackers/), [gateways/](api/v1/gateways/), [telegram/](api/v1/telegram/)

**API Conventions:**
- Content-Type: application/json
- Authentication: Session-based (check $_SESSION['username'])
- CORS enabled with credentials
- Input validation required on all endpoints

### Authentication & Sessions

Session-based authentication managed in [index.php](index.php):
- Username stored in `$_SESSION['username']`
- Role ID in `$_SESSION['roleId']`
- System ID in `$_SESSION['systemId']`
- Login via [api/v1/authentication/login.php](api/v1/authentication/login.php)
- Joins `sys_users` and `sys_hr_employee` tables
- Password verification with password_verify()
- Session regeneration on login

Role-based access control logic in [config/functions/roles.php](config/functions/roles.php).

### Component System

[config/components.php](config/components.php) defines loadable UI components from [components/](components/):
- Dashboards, forms, tables, modals, layouts, wizards
- Project-specific components (trackers, wayleaves, permits, sites)
- Partials (headers, footers, sidebars)

Components are PHP files that can be included dynamically.

### Asset Organization

- **CSS:** [assets/css/](assets/css/) - Tenant-specific bundles (KITER, UCIDOS, KUDRAT) + custom styles
- **JavaScript:** [assets/js/](assets/js/) - Core bundles + custom modules in [assets/js/custom/](assets/js/custom/)
  - [map.js](assets/js/custom/map.js), [mapgis.js](assets/js/custom/mapgis.js) - Mapbox GL integration
  - [qrcode.js](assets/js/custom/qrcode.js) - QR code generation
  - [calendar.js](assets/js/custom/calendar.js) - FullCalendar integration
- **Vendor:** [assets/plugins/](assets/plugins/) - Metronic, DataTables, Mapbox GL (no package manager)
- **Media:** [assets/media/](assets/media/) - Images, logos, icons, Lottie animations

### External Integrations

- **GeoServer:** Geospatial data querying (type name: `KITER:gis_tracer`)
- **FTP:** Document storage at storage.kutt.my
- **Telegram Bot:** Notifications via bot API
- **PWA:** [service-worker.js](service-worker.js) provides offline capability

## Key Configuration Files

- [.htaccess](.htaccess) - Apache rewrite rules (245 lines) - **Critical for routing**
- [config/system.php](config/system.php) - Multi-tenant environment loader
- [config/autoload.php](config/autoload.php) - Bootstrap and session initialization
- [config/DBFactory.php](config/DBFactory.php) - Database connection singleton
- [config/controller.php](config/controller.php) - Main controller class
- [config/functions.php](config/functions.php) - Function autoloader

## Important Patterns & Conventions

### Function Loading
[config/functions.php](config/functions.php) auto-requires all classes from [config/functions/](config/functions/). Add new function classes there and include them in functions.php.

### URL Routing
[.htaccess](.htaccess) pattern-based routing:
```apache
# Example: /projects/status → /views/projects/status.php
RewriteRule ^projects/([a-z]+)$ /views/projects/$1.php

# Example: /api/auth/login → /api/v1/authentication/login.php
RewriteRule ^api/([a-z]+)/([a-z]+)$ /api/v1/$1/$2.php
```

Modify .htaccess to add new route patterns.

### Database Queries
Always use prepared statements via DBFactory:
```php
$pdo = DBConnectionFactory::getConnection();
$stmt = $pdo->prepare("SELECT * FROM table WHERE id = ?");
$stmt->execute([$id]);
```

### API Response Format
```php
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $result]);
```

### Environment Access
```php
$config = System::getInstance();
$dbHost = $config->getDBHost();
$appUrl = $config->getAppUrl();
```

### Session Validation
Check authentication before sensitive operations:
```php
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit;
}
```

## Project-Specific Context

### Domain Logic
- **Projects:** Managed through wayleaves, permits, and site records
- **Surveys:** Team-based with priority assignments and QR code tracking
- **Tasks:** Workflow-driven with approval chains and finance integration
- **Letters:** Template-based generation with routing system
- **Geospatial:** Map visualization with project overlays

### Key Tables
- `sys_users`, `sys_hr_employee` - Authentication
- `sys_roles` - RBAC
- `gis_tracer` - Geospatial data (via GeoServer)
- Project, survey, task, letter tables (discovered from function files)

### Tenant-Specific Styling
Each tenant has CSS bundle in [assets/css/](assets/css/):
- KITER.style.bundle.css
- UCIDOS.style.bundle.css
- KUDRAT.style.bundle.css

System class loads appropriate stylesheet based on tenant.

## Security Notes

- HTTPS enforced in .htaccess
- Session IDs regenerated on login
- Password hashing with password_verify()
- Input validation needed on all user inputs (SQL injection, XSS prevention)
- Environment files contain sensitive credentials - ensure proper access controls
- Service worker handles 401 responses for expired sessions

## Troubleshooting

### Routing Issues
Check [.htaccess](.htaccess) rules and ensure mod_rewrite is enabled. Test with:
```bash
apache2ctl -M | grep rewrite
```

### Database Connection Failures
Verify credentials in environment file and PostgreSQL accessibility:
```bash
psql -h HOST -U USERNAME -d DATABASE
```

### Session Problems
Check session configuration in [config/autoload.php](config/autoload.php) and PHP session settings.

### API Errors
Enable error reporting in development (already set via display_errors in System class). Check PostgreSQL logs and Apache error logs.
