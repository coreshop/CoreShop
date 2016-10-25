# CoreShop Override Pimcore Classes

All CoreShop Classes (except Carriers) can be extended using Pimcore Classmaps

https://www.pimcore.org/wiki/display/PIMCORE4/Class-Mappings+-+Overwrite+pimcore+models

For Example

```php
//website/var/config/classmap.php
<?php

return [
    "CoreShop\\Model\\Country" => "Website\\Model\\Country",
];
```

and its implementation

```php
//website/models/Website/Model/Country.php
namespace Website\Model;

class Country extends \CoreShop\Model\Country {

    /**
     * @var
     */
    public $someVar;

    /**
     * @return mixed
     */
    public function getSomeVar()
    {
        return $this->someVar;
    }

    /**
     * @param mixed $someVar
     */
    public function setSomeVar($someVar)
    {
        $this->someVar = $someVar;
    }
}
```