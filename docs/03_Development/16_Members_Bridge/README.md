# CoreShop Members Bridge

[Members](https://github.com/dachcom-digital/pimcore-members) is a great plugin developed from [dachcom-digital](https://github.com/dachcom-digital). It can handle restriction
for Objects, Documents and Assets, as well as, handling registration and authorization.

CoreShop tries to take advantage of this great Bundle and integrates Object Restrictions for:

 - Products
 - Categories

# Installation
MembersBundle already comes with CoreShop but is inactive by default. To enable and configure it, follow these steps.

> **Tip:** If you installed CoreShop with activated MembersBridgeBundle, everything is already correctly setup and you can skip this setup.

## Adding required bundles to kernel
If you are using the full CoreShop Suite (coreshop/core-shop), simply add following to your Kernel:

```php
\CoreShop\Bundle\CoreBundle\Application\RegisterBundleHelper::registerMembers($collection);
```

Add following configuration to your config.yml

```yaml
imports:
    - { resource: "@CoreShopMembersBridgeBundle/Resources/config/app/config.yml" }
```

This will activate all needed Bundles in your Kernel.

## Pimcore DataObject Classes
Make sure your Customer and CustomerGroup class use the correct class and they have the correct fields inside.

> **Tip:** If you installed CoreShop and never modified the Customer and CustomerGroup class, simply import following files:
> **CoreShopCustomer** [CoreShopCustomer.json](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bridge/MembersBridge/Resources/install/pimcore/classes/CoreShopCustomer.json)
> **CoreShopCustomerGroup** [CoreShopCustomerGroup.json](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bridge/MembersBridge/Resources/install/pimcore/classes/CoreShopCustomerGroup.json)

If you already modified your Customer/CustomerGroup Class, you need to adjust it.

## Customer Class
Make sure class either extends from ```CoreShop\Bridge\MembersBridge\Model\Customer``` or your custom Customer class extends from it.

Members requires following fields

| Name | Field Type | Comment |
|---------------------|-------------|-------------------------------|
| userName | Input |  |
| email | Input |  |
| confirmationToken | Input | set to it read only |
| lastLogin | Date & Time | set to it read only |
| password | Password |  |
| passwordRequestedAt | Date & Time | set to it read only |
| groups | User Group | This field comes with Members |

## CustomerGroup Class
Make sure class either extends from ```CoreShop\Bridge\MembersBridge\Model\CustomerGroup``` or your custom Customer class extends from it.

Members requires following fields

| Name | Field Type | Comment |
|---------------------|-------------|-------------------------------|
| name | Input |  |
| roles | Multiselection | Set "Options Provider Class or Service Name" to `MembersBundle\CoreExtension\Provider\RoleOptionsProvider` |