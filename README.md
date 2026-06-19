# TruePoint CORS Network Website

Single-page public website plus PHP backend/admin panel for selling TruePoint RTK subscriptions through PayPal.

## What Is Included

- Public one-page website in `public/index.html`.
- Separated frontend assets:
  - `public/assets/css/styles.css`
  - `public/assets/js/app.js`
  - `public/assets/js/translations.js`
  - `public/assets/js/material.js`
  - `public/assets/images/rtk-network-hero.svg`
- PHP API endpoints:
  - `public/api/catalog.php`
  - `public/api/create-order.php`
  - `public/api/capture-order.php`
  - `public/api/webhook.php`
- Admin panel:
  - `/admin/`
  - update prices, business settings, provisioning defaults, order status, and admin password.
- Backend application code in `app/`.
- JSON storage in `app/data/`.
- App logs in `app/logs/`.

## Server Requirements

- cPanel hosting with Apache.
- PHP 8.1 or newer recommended.
- PHP extensions/functions: `curl`, `json`, `session`, `openssl`, `random_bytes`, `hash_pbkdf2`, and `mail`.
- Write permissions for:
  - `app/data`
  - `app/logs`
- HTTPS certificate enabled before live PayPal payments.

## Local Preview

You can open `public/index.html` directly in a browser to review the design. In direct-file preview, the page uses built-in fallback catalog data and shows a PayPal configuration notice instead of calling the PHP API. Full checkout requires the PHP backend on a web server.

## Admin Login

Default admin account:

- URL: `https://your-domain.com/admin/`
- Email: `admin@example.com`
- Password: `ChangeMe-TruePoint-2026!`

Change this password immediately after first login.

## PayPal Configuration

Edit `app/config.php`.

Set these fields:

```php
'paypal' => [
    'mode' => 'sandbox', // change to 'live' for production
    'client_id' => 'YOUR_PAYPAL_CLIENT_ID',
    'client_secret' => 'YOUR_PAYPAL_CLIENT_SECRET',
    'webhook_id' => 'YOUR_PAYPAL_WEBHOOK_ID',
    'currency' => 'EUR',
],
```

Where to get the values:

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/).
2. Create a REST app.
3. Copy the app `Client ID` into `client_id`.
4. Copy the app `Secret` into `client_secret`.
5. Start with `mode => 'sandbox'`.
6. Test a sandbox purchase.
7. Switch to `mode => 'live'` and replace the credentials with live credentials.

Webhook setup:

- Webhook URL: `https://your-domain.com/api/webhook.php`
- Required event: `PAYMENT.CAPTURE.COMPLETED`
- Copy the PayPal webhook ID into `webhook_id`.

The checkout path is fully server-side for sensitive operations: the browser receives only the PayPal client ID, while the secret stays in PHP.

## TruePoint / NTRIP Provisioning Configuration

Admin panel path: `/admin/dashboard.php`, section `Provisioning defaults`.

Configure:

- NTRIP host
- NTRIP port
- TLS on/off
- Default mountpoint
- Credential prefix
- Support email

Important: placeholder values such as `caster.true-point.example` must be replaced with real TruePoint-provided production values before launch. The system generates and emails a provisioning package after payment capture, but actual service validity depends on the operational TruePoint account and mountpoint rules.

## Content And Prices

Initial pricing comes from the business plan:

- 1-Month RTK Subscription: `50 EUR`
- 3-Month RTK Subscription: `120 EUR`
- 1-Year RTK Subscription: `380 EUR`

Edit prices in the admin panel or directly in `app/data/content.json`.

## Languages

The public language selector is wired in `public/assets/js/translations.js`.

Included languages:

- English
- Greek
- Turkish
- Macedonian
- Albanian
- Serbian
- Bosnian
- Croatian
- Romanian
- Estonian
- Latvian
- Lithuanian
- Hungarian

English is the source language. Localized strings are present for the public interface, with English fallback for future admin-edited content.

## Deployment On cPanel

Preferred structure:

```text
account-root/
  app/
  public/
```

Set the domain document root to:

```text
account-root/public
```

If the hosting plan cannot set the document root to `public`, put the contents of `public/` into `public_html/` and keep `app/` one level above `public_html/`. Then update the PHP `require_once` paths if your directory layout differs.

Recommended permissions:

- Directories: `755`
- Files: `644`
- `app/data`: writable by PHP
- `app/logs`: writable by PHP

## Potential Issues Requiring Admin Attention

- PayPal live payments require live credentials and HTTPS.
- PayPal card availability depends on PayPal account/country eligibility.
- `mail()` may be disabled or filtered by the host. If emails are not delivered, check `app/logs/app.log` and configure SMTP through the hosting provider.
- If `app/data` is not writable, orders and admin changes will not persist.
- If the domain document root is wrong, visitors may access backend files. Keep `app/` outside the public web root whenever possible.
- Translation text should be reviewed by native speakers before paid advertising campaigns.
- Real TruePoint/NTRIP mountpoints and account rules must be confirmed before selling live subscriptions.

## File Ownership

Frontend designers should normally work in:

- `public/index.html`
- `public/assets/css/styles.css`
- `public/assets/js/translations.js`
- `public/assets/images/`

Backend developers should normally work in:

- `app/`
- `public/api/`
- `public/admin/`
