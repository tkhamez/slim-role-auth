[![Build Status](https://api.travis-ci.org/tkhamez/slim-role-auth.svg?branch=master)](https://travis-ci.org/tkhamez/slim-role-auth)

# Role based authorization

Middleware for the [Slim framework](http://www.slimframework.com/).

## Installation

With Composer:

```
composer require tkhamez/slim-role-auth
```

## Usage

Example:

```php
$app = new Slim\App();

$app->add(new SecureRouteMiddleware([
    '/secured/public' => ['any'],
    '/secured' => ['user'],
]));

$app->add(new RoleMiddleware(
    new RoleProvider(), // must implement RoleProviderInterface
    ['route_pattern' => ['/secured']]
));
```
You can add several role providers for different paths.

This needs the Slim setting `determineRouteBeforeAppMiddleware` set to true.

For more information, see the inline documentation for the classes.
