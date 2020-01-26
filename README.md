[![Build Status](https://api.travis-ci.org/tkhamez/slim-role-auth.svg?branch=master)](https://travis-ci.org/tkhamez/slim-role-auth)
[![Test Coverage](https://api.codeclimate.com/v1/badges/72e1c7e619d44ccd001b/test_coverage)](https://codeclimate.com/github/tkhamez/slim-role-auth/test_coverage)

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

// Deny access if a required role is missing
$app->add(new SecureRouteMiddleware(
    new Slim\Psr7\Factory\ResponseFactory(), // any implementation of Psr\Http\Message\ResponseFactoryInterface
    [
        // route pattern -> roles, first "starts-with" match is used
        '/secured/public' => ['any'],
        '/secured'        => ['user'],
    ],
    ['redirect_url' => null] // optionally add "Location" header instead of 403 status code
));

// Add roles to request attribute
$app->add(new RoleMiddleware(
    new App\RoleProvider(), // any implementation of Tkhamez\Slim\RoleAuth\RoleProviderInterface
    ['route_pattern' => ['/secured']] // optionally limit to these routes
));

// Add routing middleware last, so the `route` attribute from `$request` is available
// (this replaces the determineRouteBeforeAppMiddleware setting from Slim 3).
$app->addRoutingMiddleware();
```

- The `SecureRouteMiddleware` denies access to a route if the required role is missing in the `roles` 
  request attribute.
- The `RoleMiddleware` class adds the `roles` attribute to the request object with roles provided by the 
  `RoleProvider` class.
- You can add several role providers for different paths.

For more information, see the inline documentation for the classes.

## Changelog

### 2.0.1

Compatibility with Slim 4.4

### 2.0.0

Update for Slim 4.

### 1.0.0

First stable release.
