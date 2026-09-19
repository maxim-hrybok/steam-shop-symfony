# Steam Card Shop - Enterprise E-Commerce Platform (Symfony 8 Edition)

![Symfony](https://img.shields.io/badge/Symfony-8.x-000000?style=flat-square&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![Doctrine](https://img.shields.io/badge/Doctrine-ORM-blue?style=flat-square)
![Twig](https://img.shields.io/badge/Twig-Templating-8A9b55?style=flat-square)

A full-stack e-commerce platform rebuilt from the ground up using **Symfony 8**. 

*Note: This project is an evolutionary upgrade from my previous custom PHP/MVC implementation. It demonstrates my ability to transition from raw PHP architecture enterprise-grade frameworks, utilizing modern Design Patterns, Dependency Injection, and Event-Driven Architecture.*

---

## Architectural & Engineering Highlights

This project was engineered following "The Symfony Way" to mimic enterprise-level standards:

*   **Doctrine ORM & Migrations:** Replaced raw SQL with rich PHP Entities. Utilizes lazy loading, `OneToMany`/`ManyToMany` relations, and automated schema migrations.
*   **Service-Oriented Architecture (SOA):** "Thin Controllers" handle HTTP request/response lifecycles, while complex business logic (e.g., file uploads, shopping cart calculations) is delegated to strictly typed Services.
*   **Event-Driven Architecture:** Utilizes Symfony's `EventSubscriberInterface`. For example, Google reCAPTCHA validation during login is intercepted cleanly via `CheckPassportEvent` before core authentication occurs.
*   **Advanced Form Component:** Uses Symfony Forms with Data Mappers and Custom Validation Constraints (e.g., `#[Recaptcha]`) to safely handle user inputs, CSRF protection, and file uploads.
*   **Security Component:** Robust authentication and role-based access control (RBAC). Passwords are hashed using the state-of-the-art `Argon2id` algorithm.
*   **AssetMapper:** Modern, Node.js-free frontend asset management for CSS optimization and cache-busting.
*   **QueryBuilder & Pagination:** Dynamic, database-level filtering and pagination to efficiently handle large product catalogs without loading unnecessary data into memory.
*   **Data Fixtures:** Implemented `DoctrineFixturesBundle` for instant generation of test data (Admin accounts, Categories, Products).

---

## Key Features

#### Customer-Facing Features:
*   **Dynamic Product Catalog:** Browse products with real-time price calculations (discount handling), pagination, and Category/Search filters.
*   **Shopping Cart:** Persistent, session-based cart managed elegantly via Symfony's `RequestStack`.
*   **Checkout & Inventory Protection:** Atomic stock verification during checkout to prevent race conditions and overselling.
*   **Interactive Comment System:** Authenticated users can leave reviews (with Emoji support). Comments enter a 'pending' state for admin moderation.
*   **User Authentication:** Secure registration and login, fortified by custom-integrated **Google reCAPTCHA v2**.

#### Administrative Panel (Protected by `ROLE_ADMIN`):
*   **Full Product Management (CRUD):** Add/Edit products, upload cover images via a dedicated `FileUploader` service (with strict MIME validation), and manage stock/discounts.
*   **Category Management (CRUD):** Manage product categories with dynamic Doctrine relations mapping.
*   **Order Management:** View customer orders, track purchased items, and update order statuses (Pending, Completed, Cancelled).
*   **Comment Moderation:** Dedicated dashboard to review, approve, or reject user comments before they appear publicly.

---

## Technology Stack

*   **Framework:** Symfony 8 (Webapp structure)
*   **Database:** MariaDB / MySQL
*   **ORM:** Doctrine (DBAL & ORM)
*   **Templating:** Twig (with Template Inheritance & custom Macros)
*   **HTTP Client:** Symfony HttpClient for seamless third-party API communication (Google APIs).
*   **Styling:** Custom Premium Dark Theme (CSS Variables, Flexbox, Glassmorphism).

---

## Local Setup & Installation

To run this project locally, ensure you have PHP 8.2+, Composer, and Symfony CLI installed.

### 1. Clone the repository
```bash
git clone https://github.com/maxim-hrybok/steam-shop-symfony.git
cd steam-shop-symfony
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Duplicate the .env file to .env.local and configure your database credentials and API keys:
```bash
DATABASE_URL="mysql://root:yourpassword@127.0.0.1:3306/project_symfony?serverVersion=10.11.2-MariaDB&charset=utf8mb4"

RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
```

### 4. Database Setup & Initialization
Run the following commands to create the database, generate tables, and seed it with test data (including an Admin account):
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

5. Start the Application
Use the high-performance Symfony local server:
```bash
symfony server:start
```
Frontend: Navigate to http://localhost:8000
Admin Access: Log in using adminka@gmail.com / adminka

*Developed by Maxim Hrybok.*