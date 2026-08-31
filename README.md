# ACTA Chemical

Laravel backend for the ACTA Chemical website, administration panel, and mobile
e-commerce application. The platform provides bilingual Arabic/English content,
a product catalog, customer carts and wishlists, order checkout, and EGP payments
through Paymob or cash on delivery.

## Technology stack

- PHP 8.3+
- Laravel 13
- MySQL for local, staging, and production environments
- Laravel Sanctum for mobile API authentication
- Filament 5 for administration
- Spatie Laravel Media Library for category and product images
- Scribe for API and OpenAPI documentation
- PHPUnit 12
- Vite and Tailwind CSS 4

## Application domains

### Website content

The public website API exposes settings, editable site content, events, articles,
and the contact form. Filament resources provide administration for these areas.

### Mobile commerce

The versioned `/api/v1` API provides:

- Customer registration, login, profile retrieval, and logout
- Localized category and product discovery
- Search, filters, sorting, and pagination
- Delivery-address management
- Persistent carts with stock validation
- Customer wishlists
- Server-calculated checkout
- Order history, order details, and pending-order cancellation
- Paymob and cash-on-delivery payment initialization
- HMAC-authenticated Paymob transaction callbacks

All commerce amounts are stored as integer piastres and use `EGP` as the currency.

## Project structure

```text
app/
├── Commerce/Actions/        # Checkout and order business rules
├── Filament/Resources/      # Administration resources
├── Http/
│   ├── Controllers/Api/V1/  # Versioned mobile API controllers
│   ├── Requests/            # API validation
│   └── Resources/           # API response transformation
├── Models/                  # Eloquent models and relationships
├── Payments/                # Gateway contracts, adapters, enums, and actions
└── Policies/                # Record-level authorization
```

Controllers should remain thin. New commerce business rules belong in focused
Actions or Services, validation belongs in Form Requests, and API output belongs
in API Resources.

## Local setup

This project uses directly installed PHP, MySQL, Node.js, and npm. Docker and
Laravel Sail are not required.

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure the MySQL connection in `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=acta_chemical
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Run migrations and seed initial website content:

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. Create the public storage link:

   ```bash
   php artisan storage:link
   ```

6. Install frontend dependencies and build assets:

   ```bash
   npm install
   npm run build
   ```

7. Start the application and queue worker:

   ```bash
   composer run dev
   ```

The administration panel is available at `/admin`, and generated API
documentation is available at `/docs`.

## Environment variables

### Application

```env
APP_NAME="ACTA Chemical"
APP_ENV=local
APP_URL=http://localhost:8000
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

Use separate `.env` values for development, staging, and production. Never commit
credentials or production secrets.

### Commerce

Delivery fees are configured in piastres:

```env
COMMERCE_DELIVERY_FEE_CENTS=0
```

### Paymob

```env
PAYMOB_SECRET_KEY=
PAYMOB_PUBLIC_KEY=
PAYMOB_HMAC_SECRET=
PAYMOB_INTEGRATION_ID=
PAYMOB_REDIRECT_URL=
```

Optional Paymob endpoint overrides:

```env
PAYMOB_BASE_URL=https://accept.paymob.com
PAYMOB_CHECKOUT_URL=https://accept.paymob.com/unifiedcheckout
```

Configure the following processed-transaction webhook in the Paymob dashboard:

```text
https://your-domain.example/api/v1/payments/paymob/webhook
```

Use Paymob test credentials outside production. The API secret and HMAC secret
must never be sent to the mobile application.

### Media Library

```env
MEDIA_DISK=public
MEDIA_CONVERSIONS_DISK=public
IMAGE_DRIVER=gd
```

Categories use a single `image` collection. Products use a single `thumbnail`
collection and a multi-file `gallery` collection. A `thumb` conversion is
generated for mobile catalog views.

## API conventions

Successful and failed versioned API responses use this envelope:

```json
{
  "data": {},
  "message": "Human-readable result",
  "errors": null
}
```

Paginated responses additionally include:

```json
{
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

Send `Accept-Language: ar` for Arabic localized values. Any other locale falls
back to English, and missing localized values fall back to the alternate language.

## Authentication

Register a customer:

```http
POST /api/v1/auth/register
Content-Type: application/json

{
  "name": "Mobile Customer",
  "email": "customer@example.com",
  "phone": "+201000000001",
  "password": "Password123",
  "password_confirmation": "Password123",
  "device_name": "iPhone"
}
```

Login uses the same `email`, `password`, and `device_name` fields. Both endpoints
return a Sanctum token. Send it to protected endpoints as:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

## API endpoints

### Public endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `POST` | `/api/v1/auth/register` | Register a customer |
| `POST` | `/api/v1/auth/login` | Create an access token |
| `GET` | `/api/v1/categories` | List active categories |
| `GET` | `/api/v1/categories/{slug}` | Retrieve a category |
| `GET` | `/api/v1/products` | Search and filter products |
| `GET` | `/api/v1/products/{slug}` | Retrieve a product |
| `GET` | `/api/v1/payment-methods` | List available EGP payment methods |
| `POST` | `/api/v1/payments/paymob/webhook` | Receive Paymob callbacks |

Product-list query parameters:

| Parameter | Description |
| --- | --- |
| `search` | Search English/Arabic names and SKU |
| `category` | Category ID or slug |
| `featured` | Return featured products |
| `in_stock` | Return products currently available |
| `min_price` | Minimum product price |
| `max_price` | Maximum product price |
| `sort` | `featured`, `price_asc`, `price_desc`, `latest`, or `name` |
| `page` | Pagination page |
| `per_page` | Page size, limited to 50 |

### Authenticated endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `GET` | `/api/v1/auth/profile` | Retrieve the current customer |
| `POST` | `/api/v1/auth/logout` | Revoke the current token |
| `GET` | `/api/v1/addresses` | List delivery addresses |
| `POST` | `/api/v1/addresses` | Create an address |
| `PUT/PATCH` | `/api/v1/addresses/{id}` | Update an owned address |
| `DELETE` | `/api/v1/addresses/{id}` | Soft-delete an owned address |
| `GET` | `/api/v1/cart` | Retrieve the current cart |
| `POST` | `/api/v1/cart/items` | Add a product to the cart |
| `PUT` | `/api/v1/cart/items/{id}` | Change item quantity |
| `DELETE` | `/api/v1/cart/items/{id}` | Remove a cart item |
| `GET` | `/api/v1/wishlist` | Retrieve the wishlist |
| `POST` | `/api/v1/wishlist/{productSlug}` | Add a product |
| `DELETE` | `/api/v1/wishlist/{productSlug}` | Remove a product |
| `POST` | `/api/v1/checkout` | Create an order from the cart |
| `GET` | `/api/v1/orders` | List customer orders |
| `GET` | `/api/v1/orders/{reference}` | Retrieve an owned order |
| `POST` | `/api/v1/orders/{reference}/cancel` | Cancel a pending unpaid order |
| `POST` | `/api/v1/payments` | Retry payment for an order |
| `GET` | `/api/v1/payments/{reference}` | Retrieve an owned payment |

## Checkout and payment flow

1. The customer adds active products to their cart.
2. `POST /api/v1/checkout` receives an owned `address_id`, a
   `payment_method`, and optional `customer_notes`.
3. The backend locks product rows, rechecks availability, calculates totals from
   database prices, decrements tracked inventory, snapshots the order data, and
   clears the cart.
4. For cash on delivery, the payment remains pending until cash collection is
   confirmed operationally.
5. For Paymob, the backend creates an intention and returns the public key,
   client secret, and hosted checkout URL.
6. The mobile application uses the Paymob SDK or hosted checkout to collect
   payment details. Card information must never pass through this backend.
7. Paymob calls the backend webhook. The callback HMAC is verified before the
   payment and order status are updated.

Example checkout request:

```json
{
  "address_id": 1,
  "payment_method": "paymob",
  "customer_notes": "Call before delivery"
}
```

Supported payment-method codes are `paymob` and `cash_on_delivery`.

## Database overview

| Domain | Tables |
| --- | --- |
| Authentication | `users`, `personal_access_tokens`, `password_reset_tokens`, `sessions` |
| Website | `settings`, `site_contents`, `events`, `articles`, `contacts` |
| Catalog | `categories`, `products`, `media` |
| Customer | `addresses`, `carts`, `cart_items`, `wishlists` |
| Orders | `orders`, `order_items` |
| Payments | `payment_transactions` |
| Infrastructure | `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` |

Recoverable customer addresses, categories, products, and orders use soft deletes.
Orders snapshot product names, SKU, prices, and delivery details so historical
records are preserved if catalog or address data changes.

## Development commands

```bash
# Format PHP files
vendor/bin/pint

# Run all backend tests
php artisan test

# Run commerce tests only
php artisan test tests/Feature/Commerce tests/Feature/Payments

# Verify migrations in an isolated SQLite database
DB_CONNECTION=sqlite DB_DATABASE=':memory:' php artisan migrate:fresh --force

# Generate Scribe HTML, Postman, and OpenAPI documentation
php artisan scribe:generate

# Build production frontend assets
npm run build
```

## Testing status

Commerce, catalog, media, authentication, authorization, inventory, checkout,
and payment callback behavior have feature coverage. Tests use an isolated
in-memory SQLite database and fake external Paymob and storage interactions.

The generated Laravel `ExampleTest` currently expects `/` to return `200`, while
the application intentionally redirects `/` to `/admin` with `302`. Update or
remove that placeholder test before requiring a completely green suite.

## Production-readiness checklist

The following work should be completed before accepting real orders:

- Restrict Filament access to explicit administrator roles or permissions.
- Add Filament resources and policies for categories, products, orders, and
  payment operations.
- Verify Paymob callback amount, currency, and integration ID against the stored
  transaction before marking it paid.
- Add checkout and payment idempotency keys to prevent duplicate attempts.
- Add rate limits to authentication, contact, checkout, and payment endpoints.
- Add a complete fulfillment workflow for confirmation, processing, shipping,
  delivery, COD collection, refunds, and inventory compensation.
- Queue email, media processing, and other external or long-running operations.
- Enable Sanctum bearer authentication in Scribe configuration and document
  request/response schemas for every endpoint.
- Add MySQL integration tests, concurrency tests, and Paymob failure/retry tests.
- Add CI checks for Pint, tests, dependency auditing, frontend build, and API
  documentation generation.
- Commit an npm lockfile for reproducible frontend builds.
- Configure caching and invalidation for public settings, categories, and
  featured products.

Do not enable live Paymob credentials until the payment reconciliation and
idempotency items above are complete.

## Security and operations

- Keep `APP_DEBUG=false` in staging and production.
- Serve the application only over HTTPS in production.
- Use a production queue worker and monitor failed jobs.
- Back up the database and media storage independently.
- Rotate Paymob and application secrets through environment configuration.
- Never log card details, access tokens, passwords, or Paymob secrets.
- Run dependency audits regularly:

  ```bash
  composer audit --locked
  npm audit
  ```

## License

This repository is proprietary to ACTA Chemical and Marketopia unless a separate
license agreement states otherwise.
