<div align="center">

# NerdTech License Manager

**Zero-Touch Domain-Based SaaS Licensing Package for Laravel**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nerdtech/license-manager.svg?style=flat-square)](https://packagist.org/packages/nerdtech/license-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/nerdtech/license-manager.svg?style=flat-square)](https://packagist.org/packages/nerdtech/license-manager)
[![License](https://img.shields.io/packagist/l/nerdtech/license-manager.svg?style=flat-square)](https://packagist.org/packages/nerdtech/license-manager)

</div>

The **NerdTech License Manager** is a lightweight, drop-in Laravel package designed to seamlessly validate your SaaS application's licenses across different domains. It provides a robust and fail-safe middleware that automatically checks the current domain against your central license server.

## ✨ Key Features

- **Zero-Touch Integration**: Simply install the package, and the license validation middleware is automatically injected into your web routes. No manual route modifications required!
- **Domain-Based Validation**: Authenticates licenses based on the host domain requesting access, perfect for multi-tenant and white-labeled SaaS environments.
- **Fail-Safe API Connection**: Gracefully handles API timeouts and connection failures. If the license server is unreachable, the application continues to run without interruption, ensuring maximum uptime for your users.
- **Easy Configuration**: Publish the config and set a single environment variable to get started.

## 🚀 Installation

You can install the package via composer:

```bash
composer require nerdtech/license-manager
```

## ⚙️ Usage & Configuration

Once installed, publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --provider="NerdTech\LicenseManager\LicenseManagerServiceProvider" --tag="config"
```

This will create a `config/license-manager.php` file in your application.

Next, define your central license server URL in your application's `.env` file:

```env
NERDTECH_LICENSE_SERVER_URL=https://your-central-license-server.com
```

### How it works

The package automatically registers the `VerifyLicense` middleware to your application's `web` middleware group. 

On every request, the middleware extracts the current domain and sends a verification request to your configured `NERDTECH_LICENSE_SERVER_URL`.
- If the license server responds with an invalid status, the user receives an abort response.
- If the license server is unreachable or times out, the middleware acts in a **fail-safe** manner, allowing the request to proceed so your application remains accessible.

## 🛡️ Security

If you discover any security related issues, please email security@nerdtech.com instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
