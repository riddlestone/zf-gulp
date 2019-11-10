# Clockwork Console

A [Zend Framework 2](https://github.com/zendframework) module for [Gulp](https://gulpjs.com/) configuration

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
composer require clockwork/gulp
```

## Usage

To include your Gulp pipeline in the generated configuration file, create a `Clockwork\Gulp\ProviderInterface`
implementation, and add it to your module's config file:

```php
<?php
return [
    'gulp' => [
        'providers' => [
            'My\\GulpProvider',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'My\\GulpProvider' => 'My\\GulpProviderFactory',
        ],
    ],
];
```

Have the provider return an array of `Clockwork\Gulp\PipelineInterface` objects.

You can then generate `gulpfile.js` using the console command :
```sh
vendor/bin/console gulp
```

## Get Involved

File issues at https://github.com/ariddlestone/clockwork-gulp/issues
