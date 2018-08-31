CoreShop Pimcore Bundle
=======================

# CoreShop
This Component is part of the CoreShop Project (https://www.github.com/coreshop/CoreShop). But it's designed to be used
without as well.

# Features
## Class Installer
Handles installation of Pimcore Classes, Objectbricks and Fieldcollections.

## Twig Extensions
## Tests

Test if a element is instance of a certain Pimcore Element like: Asset, Object, Document and all it subtypes:

```twig
{% if news is object %}

{% endif %}

{% if news is object_class('News') %}

{% endif %}

{% if document is document_page %}

{% endif %}

{% if image is asset_image %}

{% endif %}
```

Documentation
-------------

Documentation is available on [**coreshop.org**](https://www.coreshop.org/docs/2.0.0/).

Bug tracking
------------

CoreShop uses [GitHub issues](https://github.com/CoreShop/coreshop/issues).

GPL License
-----------

License can be found [here](https://github.com/coreshop/CoreShop/blob/master/LICENSE.md).