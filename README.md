# ArthaVidhi - Billing & Management Solution

A comprehensive billing and management solution built with Laravel (PHP) and Blade templates with Tailwind CSS.

## Features

- **Dashboard** - Overview with key metrics, charts, and recent activities
- **Bills Management** - Create, edit, view, and print invoices
- **Quotations** - Create quotations and convert them to bills
- **Products & Inventory** - Manage products, categories, and stock levels
- **Purchases** - Track purchase orders and update inventory
- **Expenses** - Record and categorize business expenses
- **Employees** - Manage employee records and attendance
- **Reports** - Sales, inventory, expenses, profit & loss, customer, and tax reports
- **Settings** - Company profile, user settings, and billing configuration

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL/MariaDB
- Apache/Nginx (or use Laravel's built-in server)

## Installation

1. **Clone or download the project**
   ```bash
   cd d:\billing\backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   copy .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Configure database**
   
   Open `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=arthavidhi
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Create the database**
   ```sql
   CREATE DATABASE arthavidhi;
   ```

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Create storage link for file uploads**
   ```bash
   php artisan storage:link
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    
    Open your browser and visit: `http://localhost:8000`

## Default Credentials

After running migrations, you can register a new account from the login page.

## Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Api/          # API Controllers
│   │       └── Web/          # Web Controllers (Blade)
│   └── Models/               # Eloquent Models
├── database/
│   └── migrations/           # Database Migrations
├── resources/
│   └── views/
│       ├── layouts/          # Base layout template
│       ├── auth/             # Login & Register views
│       ├── bills/            # Bill management views
│       ├── products/         # Product views
│       ├── quotations/       # Quotation views
│       ├── purchases/        # Purchase views
│       ├── expenses/         # Expense views
│       ├── categories/       # Category views
│       ├── employees/        # Employee views
│       ├── attendance/       # Attendance views
│       ├── reports/          # Report views
│       ├── settings/         # Settings views
│       └── pdf/              # PDF templates
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
└── public/                   # Public assets
```

## Technologies Used

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade Templates, Tailwind CSS (CDN), Alpine.js
- **Database:** MySQL with Eloquent ORM
- **PDF Generation:** DomPDF
- **Icons:** Font Awesome
- **Charts:** Chart.js

## License

This project is open-sourced software.
