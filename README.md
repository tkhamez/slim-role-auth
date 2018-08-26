# Role based authorization

[![Build Status](https://api.travis-ci.org/tkhamez/slim-role-auth.svg?branch=master)](https://travis-ci.org/tkhamez/slim-role-auth)

Middleware for the Slim framework.

Example of use:
```php
$app = new Slim\App();

$app->add(new SecureRouteMiddleware([
    '/secured/public' => ['anonymous', 'user'],
    '/secured' => ['user'],
]));

$roleProvider = new RoleProvider() // must implement RoleProviderInterface
$app->add(new RoleMiddleware(
    $roleProvider,
    ['route_pattern' => ['/secured']]
));
```
You can add several role providers for different paths.

This needs the Slim setting `determineRouteBeforeAppMiddleware` set to true.

For more information, see the inline documentation for the classes.
