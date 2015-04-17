# EventEmitter

EventEmitter is a very simple event dispatching library for PHP. It is a fork of
[Événement](https://github.com/igorw/evenement) aiming to provide additional functionality
and PHP 5.6 features like variadic arguments.

It is very strongly inspired by the EventEmitter API found in
[node.js](http://nodejs.org).

[![Build Status](https://secure.travis-ci.org/peridot-php/event-emitter.png?branch=master)](http://travis-ci.org/peridot-php/event-emitter)

## Fetch

The recommended way to install EventEmitter is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "peridot/event-emitter": "2.0.*"
    }
}
```

And run these two commands to install it:

```
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

Now you can add the autoloader, and you will have access to the library:

```php
<?php
require 'vendor/autoload.php';
```

## Usage

### Creating an Emitter

```php
<?php
$emitter = new Peridot\EventEmitter();
```

### Adding Listeners

```php
<?php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

### Emitting Events

```php
<?php
$emitter->emit('user.created', $user);
```

Tests
-----

    $ vendor/bin/phpunit

License
-------
MIT, see LICENSE.
