EzPersistenceBundle
===================

This bundle provides easy access to Zeta Components PersistentObjects in eZ Publish 5.

WARNING
-------

Since Zeta Components can only use the wrapped EzcDbHandler, the SQL queries it generates will not be displayed with SmileEzSqlProfiler.

Installation
------------

### Step 1-A: Download EzPersistenceBundle using composer

Add the bundle in your composer.json:

```js
{
    "require": {
        "smile/ez-persistence-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update smile/ez-sqlprofiler-bundle
```

Composer will install the bundle to your project's `vendor/smile/ez-persistence-bundle` directory.
Don't forget th change the vendor-dir if you are using Ez Publish Enterprise.

### Step 1-B : Download EzPersistenceBundle using your fingers

That's the other way to go for Ez Publish Enterprise as EzSystem do not provide the composer.json.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Smile\EzPersistenceBundle\SmileEzPersistenceBundle(),
    );
}
```
