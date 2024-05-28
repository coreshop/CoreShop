# Customer Registration Types

In CoreShop, the customer registration process can be configured to suit different requirements. By default,
registration requires a unique and valid email address, but you can customize this to use other identifiers like a
username.

## Register By Email

The default registration method in CoreShop is by using a unique and valid email address.

To configure registration by email, set the login identifier as follows:

```yaml
core_shop_customer:
  login_identifier: 'email'
```

## Register By Username

To enable registration via a unique username:

1. **Add a `username` field**: Ensure that your customer data object has a `username` field. By default, CoreShop does
   not include this field to avoid confusion. You can add a text field named `username` in your class editor.

2. **Change the identifier**: Set the login identifier to `username`:

```yaml
core_shop_customer:
  login_identifier: 'username'
```

## Security Measures

### Frontend Form Constraints

CoreShop includes built-in constraints that inform customers whether the email address or username they enter (depending
on your configuration) is valid and unique.

### Backend/API Validation

In addition to frontend validation, CoreShop also ensures the uniqueness of customer data when updating customer
entities via the API or backend.

> **Note**: These uniqueness checks apply only to non-guest entities.
