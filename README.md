TempoJsConfigurationBundle
==================

This bundle allows you to expose configuration in your JavaScript code.


Installation
------------

Require [`tempo/jsconfiguration-bundle`](https://packagist.org/packages/tempo/jsconfiguration-bundle)
into your `composer.json` file:


``` json
{
    "require": {
        "tempo-project/jsconfiguration-bundle": "dev-master"
    }
}
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Tempo\Bundle\JsConfigurationBundle\TempoJsConfigurationBundle(),
    );
}
```

Publish assets:

    $ php app/console tempo:js-configuration:dump
    $ php app/console assets:install --symlink web

Usage
-----


Moreover, you can configure a list of configuration to expose in app/config/config.yml:


``` yaml
# app/config/config.yml

tempo_js_configuration:
  config_to_expose: [mopa_bootstrap.form.show_legend]
```

Add these two lines in your layout:

```
<script src="{{ asset('bundles/tempojsconfiguration/js/configuration.js') }}"></script>
<script src="{{ asset('js/tempo_configuration.js') }}"></script>
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

$ composer install --dev

Run it using PHPUnit:

$ phpunit


Resources
---------

  * [Report issues](https://github.com/tempo-project/tempo) and
    [send Pull Requests](https://github.com/tempo-project/tempo/pulls)
    in the [main Tempo repository](https://github.com/tempo-project/tempo)
