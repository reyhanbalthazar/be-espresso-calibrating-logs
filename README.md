<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Espresso Calibration App

A Laravel application for tracking and calibrating espresso shots to achieve the perfect extraction. This application helps coffee enthusiasts and professionals log and analyze their espresso brewing parameters to find optimal settings.

## Features

- **User Authentication**: Secure registration and login system with Laravel Sanctum
- **Bean Management**: Track different coffee beans with details like roaster, roast level, origin, and variety
- **Grinder Management**: Catalog different grinders used for calibration
- **Calibration Sessions**: Create sessions to test and track different espresso parameters
- **Shot Tracking**: Log individual shots with grind settings, dose, yield, time, taste notes, and actions taken
- **Data Analysis**: View and analyze calibration sessions to identify optimal settings

## API Endpoints

All API endpoints are available at `/api/` and require authentication using Laravel Sanctum tokens.

### Authentication Endpoints
- `POST /api/register` - Create a new user account
- `POST /api/login` - Authenticate and receive token
- `POST /api/logout` - Revoke user tokens
- `GET /api/user` - Get current user details

### Resource Endpoints
- `GET/POST/PUT/DELETE /api/beans` - Manage coffee beans
- `GET/POST/PUT/DELETE /api/grinders` - Manage grinders
- `GET/POST/PUT/DELETE /api/calibration-sessions` - Manage calibration sessions
- `GET/POST/PUT/DELETE /api/calibration-sessions/{id}/shots` - Manage shots within sessions

Detailed API documentation is available in the `backend-docs.txt` file.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/espresso-calibrating-logs.git
   cd espresso-calibrating-logs
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies (if needed):
   ```bash
   npm install
   ```

4. Create a copy of the `.env` file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database settings in the `.env` file

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage

This application is designed to work with a frontend application. API documentation is provided to help frontend developers implement the UI/UX. The application follows RESTful API principles and uses Laravel Sanctum for authentication.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
