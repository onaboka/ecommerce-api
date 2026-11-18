# E-commerce REST API

A RESTful e-commerce API built with PHP 8.5 and PostgreSQL, running in
Docker.

## Technology Stack

-   PHP 8.5
-   PostgreSQL 16
-   Docker and Docker Compose
-   Composer
-   PDO for database access
-   PHP dotenv for environment configuration

------------------------------------------------------------------------

# Phase 1: Project and Docker Setup

## 1. Create the project structure

Created the initial project directory and organised the application into
separate folders:

``` text
ecommerce-api/
├── public/
│   └── index.php
├── src/
│   ├── Controllers/
│   ├── Services/
│   ├── Repositories/
│   ├── Models/
│   ├── Payment/
│   ├── Observers/
│   ├── Exceptions/
│   └── Database/
├── database/
│   └── migrations/
├── tests/
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── .gitignore
└── README.md
```

## 2. Configure Docker

Created a `Dockerfile` using PHP 8.5 CLI.

Installed the required PHP extensions:

-   `pdo`
-   `pdo_pgsql`

Installed Composer inside the PHP container and configured the
application to run on port `8013`.

## 3. Configure Docker Compose

Created two services:

-   `app` --- PHP application.
-   `db` --- PostgreSQL database.

Configured the database port mapping:

``` yaml
ports:
  - "5452:5432"
```

The PHP application connects to PostgreSQL internally using `db:5432`.
The database is accessible from the host machine using `localhost:5452`.

Configured a Docker volume to persist PostgreSQL data and a health check
to ensure the database is ready before the application starts.

## 4. Configure environment variables

Created a `.env` file containing application and database configuration:

``` dotenv
APP_ENV=local
APP_DEBUG=true

DB_HOST=db
DB_PORT=5432
DB_DATABASE=ecommerce
DB_USERNAME=ecommerce_user
DB_PASSWORD=your_local_password

STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
```

Added `.env` and generated or sensitive files to `.gitignore`. The real
database password must remain local and must not be committed to Git.

## 5. Configure Composer and PSR-4 autoloading

Initialised Composer and configured the project namespace:

``` json
"autoload": {
    "psr-4": {
        "App\\": "src/"
    }
}
```

Installed PHP dotenv:

``` bash
composer require vlucas/phpdotenv
```

Generated the Composer autoloader:

``` bash
composer dump-autoload
```

Successfully tested PSR-4 autoloading by creating and using
`ProductService`.

## 6. Implement the database connection

Created:

`src/Database/DatabaseConnection.php`

Implemented a PostgreSQL connection using PDO and environment variables.

Configured:

-   Exception-based error handling.
-   Associative fetch mode.
-   Native prepared statements.

Loaded environment variables using PHP dotenv.

## 7. Verify the infrastructure

Verified:

-   Docker containers.
-   PHP 8.5 runtime.
-   PostgreSQL 16.
-   Composer autoloading.
-   Environment variable loading.
-   PHP-to-PostgreSQL connectivity.

Tested the database connection by executing:

``` sql
SELECT current_database(), current_user, version();
```

**Result:** Successfully connected to the `ecommerce` database as
`ecommerce_user`.

------------------------------------------------------------------------

# Phase 2: Database Structure and Test Data

## 1. Create the products table

Created the migration file:

`database/migrations/001_create_products_table.sql`

Implemented the `products` table:

``` sql
CREATE TABLE products (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL,
    stock_quantity INTEGER NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT products_price_check CHECK (price >= 0),
    CONSTRAINT products_stock_quantity_check CHECK (stock_quantity >= 0)
);
```

### Database design decisions

-   `BIGINT GENERATED ALWAYS AS IDENTITY` generates unique product IDs.
-   `VARCHAR(255)` stores product names.
-   `TEXT` stores product descriptions.
-   `NUMERIC(10, 2)` stores monetary values with decimal precision.
-   `INTEGER` stores stock quantities.
-   `BOOLEAN` indicates whether a product is active.
-   `TIMESTAMP` stores creation and update dates.
-   `CHECK` constraints prevent negative prices and stock quantities.

## 2. Execute the migration

Executed the SQL migration using Docker:

``` bash
docker compose exec -T db psql -U ecommerce_user -d ecommerce < database/migrations/001_create_products_table.sql
```

Verified the table structure:

``` bash
docker compose exec db psql -U ecommerce_user -d ecommerce -c "\d products"
```

**Result:** Successfully created the `products` table.

## 3. Insert test products

Created:

`database/migrations/002_insert_test_products.sql`

Inserted five sample products:

1.  MacBook Pro 14
2.  iPhone 17
3.  AirPods Pro
4.  Dell UltraSharp Monitor
5.  Logitech MX Master 3S

Used a multi-row `INSERT` statement:

``` sql
INSERT INTO products (
    name,
    description,
    price,
    stock_quantity
)
VALUES
    ('MacBook Pro 14', 'Apple laptop with M-series chip', 1999.99, 10),
    ('iPhone 17', 'Apple smartphone with advanced camera', 999.99, 25),
    ('AirPods Pro', 'Wireless noise-cancelling headphones', 249.99, 50),
    ('Dell UltraSharp Monitor', '27-inch 4K professional monitor', 549.99, 15),
    ('Logitech MX Master 3S', 'Wireless ergonomic mouse', 99.99, 30);
```

Executed the SQL file:

``` bash
docker compose exec -T db psql -U ecommerce_user -d ecommerce < database/migrations/002_insert_test_products.sql
```

**Result:** Successfully inserted five products.

## 4. Retrieve and inspect products

Retrieved all products:

``` sql
SELECT * FROM products;
```

Verified that PostgreSQL correctly generated:

-   Product IDs.
-   Default `is_active` values.
-   `created_at` timestamps.
-   `updated_at` timestamps.

## 5. SQL operations practised

The following SQL operations were introduced as part of the database
learning process.

**Filter products:**

``` sql
SELECT id, name, price
FROM products
WHERE price < 500;
```

**Sort products:**

``` sql
SELECT name, price
FROM products
ORDER BY price DESC;
```

**Limit results:**

``` sql
SELECT name, price
FROM products
ORDER BY price ASC
LIMIT 3;
```

**Update a product:**

``` sql
UPDATE products
SET price = 229.99
WHERE name = 'AirPods Pro';
```

**Delete a product:**

``` sql
DELETE FROM products
WHERE name = 'Logitech MX Master 3S';
```

The update and delete statements are included as practice examples; they
should only be marked as completed after being executed.

------------------------------------------------------------------------

# Current Project Status

Component                     Status
  ----------------------------- -------------
Docker configuration          Completed
PHP 8.5 setup                 Completed
PostgreSQL 16 setup           Completed
Composer configuration        Completed
PSR-4 autoloading             Completed
PHP dotenv                    Completed
PDO database connection       Completed
Products table                Completed
Five test products inserted   Completed
Product Model                 In progress
Product Repository            Pending
Product Service integration   Pending
REST API endpoints            Pending
PHPUnit tests                 Pending
Stripe integration            Pending

------------------------------------------------------------------------

# Next Phase: Product Model and Repository

Planned implementation:

1.  Create `src/Models/Product.php`.
2.  Implement the Product model using OOP principles.
3.  Create `src/Repositories/ProductRepository.php`.
4.  Implement database queries using PDO prepared statements.
5.  Retrieve products from PostgreSQL through the repository.
6.  Connect the repository to `ProductService`.

The goal is to separate database access from business logic and
introduce the Repository Pattern.
