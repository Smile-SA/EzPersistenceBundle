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

Basic setup
-----------

### Create the schema file

``` xml
<!-- Resources/config/persistence/schema.xml -->

<?xml version="1.0" encoding="ISO-8859-1"?>
<database>
  <table>
    <name>message</name>
    <declaration>
      <field>
        <name>message_id</name>
        <type>integer</type>
        <autoincrement>true</autoincrement>
        <notnull>true</notnull>
        <default>0</default>
      </field>
      <field>
        <name>title</name>
        <type>text</type>
        <length>255</length>
        <notnull>true</notnull>
        <default></default>
      </field>
      <field>
        <name>content</name>
        <type>text</type>
        <length>255</length>
        <notnull>true</notnull>
        <default></default>
      </field>
      <index>
        <name>primary</name>
        <primary>true</primary>
        <field>
          <name>message_id</name>
          <sorting>ascending</sorting>
        </field>
      </index>
    </declaration>
  </table>
</database>
```

### Create the table

``` sql
CREATE TABLE message
(
    message_id INT UNSIGNED NOT NULL auto_increment,
    title varchar(255),
    content varchar(255),
    PRIMARY KEY (message_id)
);
```

### Generate classes and definitions

``` bash
$ php ezpublish/console persistence:generate:objects --bundle=eZDemoBundle
```

Usage
-----

### Create

``` php
use EzSystems\DemoBundle\Persistence\Message;
...
$session = $this->get('smile.persistent_session');

$message = new Message();
$message->title = 'Test';
$message->content = 'This is a test';

$session->save($message);
```

### Update

``` php
...
$message->title = 'Test2';
$session->update( $message );
```

### Load

``` php
...
$message = $session->load( 'eZDemoBundle:Message', 1 );
```

### Find

``` php
...
$q = $session->createFindQuery( 'eZDemoBundle:Message' );
$q->where( $q->expr->eq( 'title', $q->bindValue( 'Test' ) ) );
$messages = $session->find( $q, 'eZDemoBundle:Message' );
```

Refer to the [documentation](http://ezcomponents.org/docs/api/latest/introduction_PersistentObject.html) for advanced usage.