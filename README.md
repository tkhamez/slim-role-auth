[![build](https://github.com/tkhamez/slim-role-auth/workflows/test/badge.svg)](https://github.com/tkhamez/slim-role-auth/actions)
[![Test Coverage](https://api.codeclimate.com/v1/badges/72e1c7e619d44ccd001b/test_coverage)](https://codeclimate.com/github/tkhamez/slim-role-auth/test_coverage)
[![Packagist Downloads](https://img.shields.io/packagist/dt/tkhamez/slim-role-auth)](https://packagist.org/packages/tkhamez/slim-role-auth)

# Role-based authorization

Middleware for the [Slim 4](http://www.slimframework.com/) framework.

For Slim 3 use the 1.0.0 release.

## Installation

With Composer:

```
composer require tkhamez/slim-role-auth
```

## Usage

Example:

```php
use Tkhamez\Slim\RoleAuth\RoleMiddleware;
use Tkhamez\Slim\RoleAuth\SecureRouteMiddleware;

$app = Slim\Factory\AppFactory::create();

// Deny access if a required role is missing.
$app->add(new SecureRouteMiddleware(
    new Slim\Psr7\Factory\ResponseFactory(), // Any implementation of Psr\Http\Message\ResponseFactoryInterface.
    [
        // Route pattern  -> roles, first "starts-with" match is used.
        '/secured/public' => ['any'],
        '/secured'        => ['user'],
    ],
    ['redirect_url' => null] // Adds "Location" header instead of 403 status code if set.
));

// Add roles to request attribute.
$app->add(new RoleMiddleware(
    new App\RoleProvider(), // Any implementation of Tkhamez\Slim\RoleAuth\RoleProviderInterface.
    ['route_pattern' => ['/secured']] // Optionally limit to these routes.
));

// Add routing middleware last, so the Slim router is available from the request.
$app->addRoutingMiddleware();
```

- The `SecureRouteMiddleware` denies access to a route if the required role is missing in the request object.
- The `RoleMiddleware` class adds roles provided by the `RoleProvider` object to the request object.
- You can add multiple role providers for different paths.

For more information, see the inline documentation of the classes.

## Dev Env

```shell
docker build --tag slim-role-auth .
docker run -it --mount type=bind,source="$(pwd)",target=/app --workdir /app slim-role-auth /bin/sh
```

## Changelog

### 5.1.0 - 2024-11-23

- Compatibility with PHP 8.4.

### 5.0.0 - 2024-06-01

- Raised minimum required PHP version to 7.4.

### 4.0.0

- Raised minimum required PHP version to 7.3.

### 3.0.1

- Update PHP requirement to include version 8 (^7.2|^8.0).

### 3.0.0

- Raised minimum PHP version to 7.2
- Added a class constant for the name of the request attribute that holds the roles and changed its name.

### 2.0.1

- Compatibility with Slim 4.4

### 2.0.0

- Update for Slim 4.

### 1.0.0

- First stable release.
