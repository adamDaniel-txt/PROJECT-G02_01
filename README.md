# Kafe Tiga Belas - Online Ordering System

## 🚀 Quick Start

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/kafe-tiga-belas.git
cd kafe-tiga-belas
```

### 2. Setup Environment
1. Copy the example environment file:
```bash
cp .env.example .env
```

2. Edit `.env` file with your configuration:
```env
# Stripe API Keys (Test Mode)
STRIPE_PUBLIC_KEY=pk_test_your_public_key_here
STRIPE_SECRET_KEY=sk_test_your_secret_key_here

# App Configuration
APP_ENV=development
DEBUG_MODE=true
```

### 3. Import Database
Import `tigaBelasCafe.sql` to your MySQL database:
```bash
mysql -u root -p tigaBelasCafe < tigaBelasCafe.sql
```

### 4. Install Dependencies
Install Stripe PHP library:
```bash
composer require stripe/stripe-php
```

Or download manually:
```bash
mkdir -p assets/vendor
cd assets/vendor
git clone https://github.com/stripe/stripe-php.git stripe
```

### 5. Configure Web Server
- Point web server (Apache/Nginx) to project root
- Ensure PHP 7.4+ is installed
- Enable MySQL extension in PHP

## 🔧 Configuration

### Stripe Setup
1. Create Stripe account: https://stripe.com
2. Get test API keys from Dashboard → Developers → API keys
3. Add keys to `.env` file

### Database Setup
1. Create MySQL database named `tigaBelasCafe`
2. Import SQL file: `tigaBelasCafe.sql`
3. Update database credentials in `.env`

## 📁 Project Structure
```
kafe-tiga-belas/
├── .env.example           # Environment template
├── .gitignore            # Git ignore file
├── README.md             # This file
├── app/                  # Application logic
│   ├── config.php       # Configuration loader
│   ├── db.php           # Database connection
│   ├── cart_functions.php
│   ├── order_functions.php
│   └── menu_functions.php
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript
│   ├── vendor/          # Third-party libraries
│   └── img/             # Images
├── tigaBelasCafe.sql     # Database schema
├── index.php            # Home page
├── menu.php             # Menu page
├── checkout.php         # Checkout page
├── payment-success.php  # Payment success page
├── receipt.php          # Receipt page
├── order-history.php    # Order history page
├── order-details.php    # Order details page
├── order-track.php      # Order tracking page
└── login.php           # Login page
```

## 👥 User Accounts

### Pre-configured Accounts:
- **Admin**: admin@example.com / password
- **Staff**: staff@example.com / password
- **Customer**: customer@example.com / password
- **Guest**: guest@example.com / password

## 💳 Payment Testing

### Test Cards (Stripe Sandbox):
| Card Number           | Description             |
|-----------------------|-------------------------|
| `4242 4242 4242 4242` | Successful payment      |
| `4000 0000 0000 9995` | Declined payment        |
| `4000 0027 6000 3184` | Requires authentication |

**Expiry:** Any future date
**CVC:** Any 3 digits
**ZIP:** Any 5 digits
