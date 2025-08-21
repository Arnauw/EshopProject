Made at AFPA with ❤️, and a lot of coffee ☕.

# Amine Shop

Amine Shop is a robust e-commerce platform built with the Symfony framework. It features a complete administrative backend for managing products, categories, and orders, and a clean, user-facing storefront with a card-based layout. The project is integrated with Stripe for secure payments and includes PDF bill generation.

---

## Features

*   **Admin Dashboard:** A secure area for managing all aspects of the shop.
*   **Product Management:** Full CRUD (Create, Read, Update, Delete) functionality for products, including image uploads and stock management.
*   **Hierarchical Categories:** Products are organized by categories and subcategories.
*   **User Management:** Admin control over user roles (`ROLE_ADMIN`, `ROLE_EDITOR`, `ROLE_USER`).
*   **Order Management:** View and manage customer orders, with a detailed history for each product's stock changes.
*   **Shopping Cart:** A session-based shopping cart for customers.
*   **Stripe Payment Integration:** Secure payment processing using Stripe Checkout and robust webhook handling for payment confirmation.
*   **PDF Bill Generation:** Automatically generate PDF invoices for orders using Dompdf.
*   **Responsive Design:** A consistent and clean user interface built with Bootstrap 5 that works on all devices.
*   **Pagination:** Clean, paginated lists for all admin index pages using KnpPaginatorBundle.

---

## Tech Stack

*   **Framework:** Symfony 7+
*   **Database / ORM:** MariaDB/MySQL & Doctrine
*   **Frontend:** Twig with Bootstrap 5
*   **Asset Management:** Symfony AssetMapper
*   **JavaScript:** VanillaJS, Stimulus for Turbo compatibility
*   **Payments:** Stripe PHP SDK
*   **PDF Generation:** Dompdf

---

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:
*   PHP 8.2 or higher
*   Composer
*   Symfony CLI
*   Stripe CLI

---

## Local Installation

Follow these steps to get your development environment set up.

**1. Clone the repository:**
```bash
git clone <your-repository-url>
cd amine-shop
```

**2. Install PHP Dependencies:**
```bash
composer install
```

**3. Configure Environment Variables:**
Copy the environment template and fill in your local credentials.
```bash
cp .env .env.local
```
Now, open `.env.local` and configure your database and Stripe keys:
```dotenv
# .env.local

# Make sure APP_ENV is set to 'dev' for development
APP_ENV=dev
APP_DEBUG=1

# Your local database connection string
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/amine_shop_db?serverVersion=mariadb-10.11&charset=utf8mb4"

# Your Stripe API keys
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

**4. Set up the Database:**
```bash
# Create the database
php bin/console doctrine:database:create

# Run the database migrations
php bin/console doctrine:migrations:migrate
```

**5. Start the Local Server:**
```bash
symfony server:start
```
Your application should now be running at `https://127.0.0.1:8000`.

---

## Stripe Webhook Setup (Local Development)

To test the payment confirmation webhook, you must forward Stripe events to your local server.

1.  Log in to the Stripe CLI:
    ```bash
    stripe login
    ```

2.  Start forwarding events to your webhook controller. **Use `https://`**.
    ```bash
    stripe listen --forward-to https://127.0.0.1:8000/stripe/notify
    ```

3.  The CLI will output a webhook signing secret (`whsec_...`). Copy this and place it in your `.env.local` file for the `STRIPE_WEBHOOK_SECRET` variable.

---

## License

This project is not licensed.
