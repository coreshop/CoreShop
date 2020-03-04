# CoreShop Customer Registration Types
By default, a customer needs to provide a unique and valid email address to pass a registration.

## Register By Email
> This is the default setting!

To switch to registration by a unique and valid email address, you need set the identifier:

```yaml
core_shop_customer:
    login_identifier: 'email'
```

## Register By Username
To switch to registration by a unique username, you need change the identifier:

```yaml
core_shop_customer:
    login_identifier: 'username'
```

## Security

### Form (Frontend)
CoreShop comes with a preinstalled constraint which will tell your customer, if an email address or username - depending on your settings - is valid or not.

### Backend / API
Plus, if you're going to update a customer by API or Backend, coreshop also checks if your customer entity has unique data.

> Note: Both checks only apply to non-guest entities!

