# PHP Runtime Cache Trait

This trait allows you to easily add runtime caching to your PHP classes!

## Installation

```bash
composer require "Sairahcaz/php-runtime-cache-trait"
```

## Usage

### Add the trait to your class and specify available cache types

```php
<?php

use Sairahcaz\HasRuntimeCache;

class Foo
{
    use HasRuntimeCache;
    
    public function __construct()
    {
    
        ...
        
        //important! default should be null
        $this->runtimeCache = [
            'heavyDataCalculation_elements' => null,
        ];
    }
}
```

### Implementation

#### Cache all records

```php
<?php

use Sairahcaz\HasRuntimeCache;

class Foo
{
    ...
    
    public function heavyDataCalculation()
    {
        // if you want you can set a global prefix for whole function
        // or you add the prefix to each call (see below)
        // Note: this needs to match the first part defined in the constructor
        $this->runtimeCacheGlobalPrefix = 'heavyDataCalculation';
        
        //Note: the key needs to match the second part defined in the constructor
        $elements = $this->getSafeRuntimeCache('elements', function() {
            //here you return the data you want to cache
            return get_sql_data("SELECT * FROM elements WHERE 1;");
        });
    }
}
```
#### Cache single record

```php
<?php

use Sairahcaz\HasRuntimeCache;

class Foo
{
    ...
    
    public function heavyDataCalculation($elementId)
    {
        $this->runtimeCacheGlobalPrefix = 'heavyDataCalculation';
        
        $element = $this->getSafeRuntimeCache('elements', function() use ($elementId) {
            return get_sql_data("SELECT * FROM elements WHERE id = $elementId;");
        }, $elementId);//<---- Important add cache key here!!!
    }
}
```
#### Dis/Enable it

If you want to test your code without the runtime cache, or any other reason,
you can dis/enable it.

```php
<?php

use Sairahcaz\HasRuntimeCache;

class Foo
{
    use HasRuntimeCache;
    
    public function __construct()
    {
    
        ...
        
        $this->runtimeCacheEnabled = false; //<--- default is true
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.