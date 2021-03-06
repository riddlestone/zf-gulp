# Riddlestone ZF-Gulp

A [Zend Framework 2](https://github.com/zendframework) module for [Gulp](https://gulpjs.com/) configuration

> ## Repository archived 2020-02-06
>
> This repository has been replaced with [riddlestone/brokkr-gulpfile](https://github.com/riddlestone/brokkr-gulpfile).
>
> Note that the new package is much simpler, so some work will be involved in switching to use it.

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
composer require riddlestone/zf-gulp
```

## Usage

To include your Gulp pipeline in the generated configuration file, create a `Riddlestone\ZF\Gulp\ProviderInterface`
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

Have the provider return an array of `Riddlestone\ZF\Gulp\PipelineInterface` objects.

You can then generate `gulpfile.js` using the console command :
```sh
vendor/bin/console gulp
```

## Get Involved

File issues at https://github.com/riddlestone/zf-gulp/issues
