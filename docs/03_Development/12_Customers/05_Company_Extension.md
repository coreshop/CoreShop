# CoreShop Customer Company Extension
The Company Entity allows you to append customers to a given company.
After a customer has been connected to a company by using the 1to1 relation `company`, it's possible to share addresses between company and the self-assigned addresses.

## Access Types
> Note! This is only available if a customer already is connected to a valid company!

### Own Only
If set, the customer can create, edit and delete own addresses and choose them in checkout as well. This is the default behaviour.

### Company Only
If set, the customer can create, edit und delete company addresses and choose them in checkout as well. He's not able to add addresses to himself.

### Own And Company
If set, the customer can create, edit and delete company and private addresses and choose them in checkout as well. 

Plus, the `own_and_company` mode allows the customer to define and modify the allocation of the address. 
To do so, coreshop renders an additional choice type to the address creation/modification form.

**Note**: If a customer switches the allocation after it has been created, the address also physically gets moved to its desired location. 
In this example, the customer changes the allocation from `own` to `company`:

Before:
```yaml
- company A
    - addresses
    - customer A
        - addresses
            - address A
```

After:
```yaml
- company A
    - addresses
        - address A
    - customer A
        - addresses
```

Read more about this feature [here](https://github.com/coreshop/CoreShop/issues/1266).