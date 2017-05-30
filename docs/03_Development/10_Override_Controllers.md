# CoreShop Override Controller

CoreShop uses services for controllers, if you need to extend a controller, simply override the service:

```
services:
    coreshop.frontend.controller.category:
        class: AcmeBundle\Controller\CategoryController

parameters:
    coreshop:
        model:
            order:
                pimcore_controller: AcmeBundle\Controller\CategoryController
```