# Tunna Link Shortener 2.0

A clean architecture refactoring of the original link shortener application.

## Architecture Overview

This refactored application follows clean architecture principles with proper separation of concerns:

### Directory Structure

```
src/
├── Config/           # Configuration classes
├── Controllers/      # HTTP request handlers
├── Database/         # Database connection management
├── Models/          # Domain entities
├── Repositories/    # Data access layer
├── Router/          # Routing system
├── Services/        # Business logic
└── Utils/           # Utility classes

views/
├── layouts/         # Layout templates
├── partials/        # Reusable components
└── *.php           # View templates

public/             # Web-accessible files
├── assets/         # Static assets
└── index.php       # Entry point

config/             # Configuration files
```

## Key Features

- **Clean Architecture**: Proper separation of concerns with distinct layers
- **Dependency Injection**: Automatic dependency resolution
- **Repository Pattern**: Clean data access layer
- **Service Layer**: Business logic encapsulation
- **Template System**: Reusable view components
- **Configuration Management**: Environment-based configuration
- **Error Handling**: Graceful error management

## Installation

1. **Install Dependencies**

   ```bash
   composer install
   ```

2. **Run Migration Script**

   ```bash
   php migrate.php
   ```

3. **Configure Environment**

   - Copy `.env.example` to `.env` (if needed)
   - Update database credentials and other settings

4. **Update Web Server**
   - Point your web server to the `public` directory
   - Ensure URL rewriting is enabled

## Usage

The application maintains the same functionality as the original but with improved architecture:

- **Home Page**: `/`
- **Link Access**: `/{code}`
- **API Tracking**: `POST /api/tracker`
- **Redirect API**: `GET /api/redirect?next={url}`

## Architecture Benefits

### Before (Original)

- Mixed concerns in single files
- Direct database access in views
- No dependency management
- Hard to test and maintain

### After (Refactored)

- Clear separation of concerns
- Dependency injection
- Repository pattern for data access
- Service layer for business logic
- Reusable components
- Easy to test and extend

## Design Patterns Used

1. **Repository Pattern**: Data access abstraction
2. **Service Layer**: Business logic encapsulation
3. **Dependency Injection**: Automatic dependency resolution
4. **Template Method**: View rendering system
5. **Singleton**: Database connection management
6. **Factory**: Object creation patterns

## Configuration

The application uses environment-based configuration:

```php
// Database
DB_HOST=your_host
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_NAME=your_database

// Application
APP_URL=https://your-domain.com
APP_ENV=production

// reCAPTCHA
RECAPTCHA_SECRET_KEY=your_secret_key
RECAPTCHA_SITE_KEY=your_site_key
```

## Development

### Adding New Features

1. **Models**: Create domain entities in `src/Models/`
2. **Repositories**: Add data access in `src/Repositories/`
3. **Services**: Implement business logic in `src/Services/`
4. **Controllers**: Handle HTTP requests in `src/Controllers/`
5. **Views**: Create templates in `views/`

### Testing

The clean architecture makes testing much easier:

```php
// Example unit test
$linkService = new LinkService($mockRepository, $mockTrackerService);
$result = $linkService->getLinkByCode('test123');
```

## Migration from Original

The migration script (`migrate.php`) helps transition from the old architecture:

1. Copies assets to the new structure
2. Creates necessary directories
3. Provides guidance for next steps

## Security Improvements

- Input validation and sanitization
- SQL injection prevention with prepared statements
- XSS protection with proper escaping
- CSRF protection (where applicable)
- Security headers

## Performance Improvements

- Database connection pooling
- Efficient query patterns
- Optimized view rendering
- Asset caching
- Output buffering

This refactored application maintains all original functionality while providing a solid foundation for future development and maintenance.
