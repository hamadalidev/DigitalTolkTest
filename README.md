# Translation Management Service

A Laravel-based service for managing translations with device-specific content. This service allows you to store and manage translations for different locales and device types (mobile, tablet, desktop).

## Features

- Store translations for multiple locales
- Device-specific translations (mobile, tablet, desktop)
- Group translations for better organization
- RESTful API endpoints for CRUD operations
- Swagger API documentation
- Token-based authentication using Laravel Sanctum
- Caching support for better performance
- Comprehensive error handling and validation
- Data Transfer Objects (DTOs) for type safety
- Repository pattern implementation
- Service layer for business logic
- Form Request validation
- Laravel Resources for API responses
- Custom Exceptions
- Database Factories and Seeders
- Custom Artisan Commands

## Requirements

- PHP 8.3 or higher
- Laravel 12.x
- MySQL 5.7 or higher
- Composer

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <repository-directory>
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Generate Swagger documentation:
```bash
php artisan l5-swagger:generate
```

### documentaion path
http://127.0.0.1:8000/api/documentation

## Project Structure

### Interfaces
- `TranslationServiceInterface`: Defines the contract for translation service operations
- `TranslationRepositoryInterface`: Defines the contract for translation data access

### Form Requests
- `TranslationRequest`: Handles validation for translation creation and updates
- `LocaleRequest`: Handles validation for locale-related operations

### Resources
- `TranslationResource`: Transforms translation models into JSON responses
- `LocaleResource`: Transforms locale models into JSON responses

### Exceptions
- `TranslationNotFoundException`: Custom exception for missing translations
- `LocaleNotFoundException`: Custom exception for missing locales
- `InvalidDeviceTypeException`: Custom exception for invalid device types

### Swagger Documentation
- API endpoints are documented using OpenAPI/Swagger annotations
- Documentation is available at `/api/documentation`
- Includes request/response schemas, parameters, and examples

### Factories
- `TranslationFactory`: Generates test data for translations
- `LocaleFactory`: Generates test data for locales
- `UserFactory`: Generates test data for users

### Commands
- `CreateTranslation`: Artisan command to create translations via CLI
- `UpdateTranslation`: Artisan command to update translations via CLI
- `DeleteTranslation`: Artisan command to delete translations via CLI
- `GenerateTestTranslations`: Generates test translations for all locales and device types

### Seeders
- `DatabaseSeeder`: Main seeder that calls other seeders
- `UserSeeder`: Seeds default users
- `LocaleSeeder`: Seeds default locales
- `TranslationSeeder`: Seeds sample translations

## Setup and Data Generation

### Initial Setup
1. Run migrations:
```bash
php artisan migrate
```

2. Seed the database with basic data:
```bash
php artisan db:seed
```

3. Generate test translations:
```bash
# Generate default number of translations (100,000)
php artisan translations:generate

# Or specify a custom number of translations
php artisan translations:generate 50000
```

This will create:
- Default users (admin and test user)
- Default locales (en, es, fr)
- Sample translations for each locale
- Test translations for all device types (mobile, tablet, desktop)
- Random number of translations based on the specified count (default: 100,000)

### Test Data Structure
The generated test translations include:
- Common UI elements (buttons, labels, messages)
- Error messages
- Success messages
- Navigation items
- Form labels and placeholders
- Device-specific content variations

### Available Device Types
- `desktop`: Full desktop experience
- `tablet`: Tablet-optimized content
- `mobile`: Mobile-optimized content

### Translation Groups
- `general`: Common UI elements
- `auth`: Authentication related messages
- `validation`: Form validation messages
- `errors`: Error messages
- `success`: Success messages
- `navigation`: Navigation items
- `forms`: Form-related content

## API Documentation

The API documentation is available at `/api/documentation` after running the Swagger generation command.

### Authentication

All API endpoints are protected with token-based authentication using Laravel Sanctum. To access the API:

1. Create a personal access token:
```bash
php artisan sanctum:token
```

2. Include the token in your API requests:
```
Authorization: Bearer your-token-here
```

### Available Endpoints

- `GET /api/translations` - List all translations
- `POST /api/translations` - Create a new translation
- `GET /api/translations/{id}` - Get a specific translation
- `PUT /api/translations/{id}` - Update a translation
- `DELETE /api/translations/{id}` - Delete a translation
- `GET /api/translations/locale/{locale}` - Get translations by locale
- `GET /api/translations/json/{locale}` - Get translations in JSON format for frontend use

## Usage Examples

### Creating a Translation

```bash
curl -X POST http://your-domain/api/translations \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "locale_id": 1,
    "key": "welcome.message",
    "value": "Welcome to our application!",
    "device_type": "mobile",
    "group": "general",
    "is_active": true
  }'
```

### Getting Translations by Locale

```bash
curl -X GET http://your-domain/api/translations/locale/en \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json"
```

### Getting JSON Translations for Frontend

```bash
curl -X GET http://your-domain/api/translations/json/en?device_type=mobile \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json"
```

## Testing

Run the test suite:

```bash
php artisan test
```

The test suite includes:
- Unit tests for services and repositories
- Feature tests for API endpoints
- Integration tests for database operations

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request
