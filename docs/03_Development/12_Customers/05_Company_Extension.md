# Customer Company Extension

CoreShop includes a Company Entity feature, which enhances customer profiles by allowing them to be associated with
specific companies. This connection enables sharing of addresses between the company and individual customers.

## Access Types

The access type determines how a customer interacts with addresses, both their own and those of the associated company.
This feature is activated once a customer is linked to a valid company.

### Own Only

- **Default Setting**: Customers can manage their own addresses.
- **Capabilities**: Customers can create, edit, and delete their own addresses, and use them during checkout.

### Company Only

- **Company-Centric**: Customers manage company addresses.
- **Capabilities**: Customers can create, edit, and delete company addresses, choose them in checkout, but cannot add
  personal addresses.

### Own And Company

- **Dual Access**: Customers can manage both personal and company addresses.
- **Capabilities**: Creation, editing, and deletion of both personal and company addresses, with the ability to choose
  either during checkout.

Additionally, in the `own_and_company` mode, customers have the flexibility to define and modify the allocation of
addresses. CoreShop incorporates a choice type in the address form for this purpose.

**Physical Address Movement**: If a customer reallocates an address (e.g., from personal to company), the address is
physically moved to the new category. For example, changing an address from `own` to `company` results in the following
shift:

Before Allocation Change:

```yaml
- company A
    - addresses
    - customer A
        - addresses
            - address A
              ```

After Allocation Change:

```yaml
- company A
    - addresses
        - address A
    - customer A
        - addresses
          ```

For further details on this feature, refer to the discussion on [GitHub](https://github.com/coreshop/CoreShop/issues/1266).
