# GetPayInTask
Flash Sale API
A Laravel 12 API for selling limited-stock products during a flash sale.
Supports high concurrency, short-lived holds, order creation, and idempotent payment webhooks.
# Assumptions & Invariants
# Product & Stock
Only one product is seeded for flash sale testing.
Each product has a finite current_stock.
Stock cannot go below zero (current_stock >= 0).

# Holds

Holds reserve stock for a short duration (2 minutes).

Expired holds release stock automatically.

A hold can only be used once to create an order.

# Orders

Orders can only be created for valid, unexpired holds.

Each order has a payment_status (pending, paid, failed) and order_status (pre-payment, confirmed, cancelled).

# Payment Webhook

Webhook is idempotent (same idempotency_key will not duplicate payments).

Can arrive before or after the order creation.

Updates order state on success or cancels the order on failure.

Stock is released if payment fails.

# Concurrency

Uses DB::transaction and lockForUpdate() for safe stock updates.

Prevents overselling under high concurrency.

Expired holds and concurrent holds are safely managed.

Caching & Performance

Read endpoints can use Laravel cache (any driver: file, Redis, memcached, etc.).

Designed to remain fast under burst traffic.

Avoids N+1 queries for product or hold listing.

# Running the App

Clone the repository:

git clone https://github.com/AhmedEmad101/GetPayInTask.git
cd GetPayInTask


# Install dependencies:

composer install

Copy .env and configure database:

cp .env.example .env
php artisan key:generate


# Run migrations and seed one product:

php artisan migrate --seed

# Run the local development server:

php artisan serve
and open another terminal 
php artisan schedule:workRunning the Tests

# PHP unit Tests
Product endpoint returns correct stock.
Prevents overselling under concurrent holds.
Short-lived hold creation and expiry.
Order creation from valid hold.
Idempotent payment webhook handling.
Run tests using PHPUnit:
make sure you made a test database and link it in the file called .env.testing
php artisan migrate --env=testing
php artisan test --env=testing.
php artisan test
