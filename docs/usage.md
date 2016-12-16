Usage
=====

## Base usage

Adding a new value:

```php
$this->get('cache_time_keeper')->set('foo', new \DateTime());
```

Load saved date:

```php
$date = $this->get('cache_time_keeper')->get('foo');
```

Getting the newest date for a set of keys, taking into account the date of the change project:

```php
$date = $this->get('cache_time_keeper')->getMax('foo');
```

Remove value:

```php
$this->get('cache_time_keeper')->remove('foo');
```

## By entity name

Examples for entity class: `\Acme\Bundle\DemoBundle\Entity\Page`.

Get date last update any of entities:

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page');
```

Get date last update for entity by id `123`:

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page:123');
```

Get date last update for entity of composite identifier _(composite primary key)_, for example
`id = 123` and `type = foo`.

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page:123|foo');
```
