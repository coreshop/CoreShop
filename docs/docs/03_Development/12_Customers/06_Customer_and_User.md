# Customer and User

CoreShop distinguishes between two entities when it comes to customer data management: `Customer` and `User`.
Understanding the difference between these two is crucial for effectively managing customer information and
authentication.

## Distinction Between Customer and User

- **User**: The User entity in CoreShop is primarily concerned with authentication. It encompasses login credentials,
  including the login identifier (such as an email address or username) and the password.

- **Customer**: The Customer entity, on the other hand, represents the actual customer profile. This includes details
  such as contact information, shipping and billing addresses, and order history.

## Guest Customers

In scenarios where a Customer does not have an associated User entity, they are considered a 'Guest Customer'. This
means they cannot log in to the system.

### Handling Guest Customers in Checkout

During the checkout process, CoreShop can identify existing Guest Customers based on their email address. If a match is
found, CoreShop uses the existing Customer Object. This approach has several benefits:

- **Order Tracking**: Allows for the tracking of orders placed by guest customers.
- **Conversion Opportunity**: Provides an opportunity to later convert guest orders into full customer profiles,
  enhancing customer relationship management.

By maintaining separate User and Customer entities, CoreShop offers a flexible framework to cater to both registered and
guest customers, while keeping authentication and customer data management distinct yet integrated.
