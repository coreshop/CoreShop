# Money Values

CoreShop adopts an integer-based approach for storing money and currency values. This methodology is crucial for
ensuring accuracy and avoiding common issues associated with floating-point values, particularly when it comes to
financial calculations.

## Why Integer Values?

1. **Precision and Accuracy**: Floating-point arithmetic can introduce rounding errors, which are unacceptable in
   financial calculations. By using integers to represent monetary values, CoreShop ensures precise calculations without
   rounding discrepancies.

2. **Standard Practices**: Many financial systems and databases prefer integer representation for money to maintain
   consistency and precision. This approach aligns CoreShop with standard practices in financial data handling.

3. **Avoiding Floating-Point Pitfalls**: Floating points can cause unexpected behavior due to how computers handle
   decimal fractions. For example, the result of adding 0.1 and 0.2 may not precisely equal 0.3 in floating-point
   arithmetic. Such inaccuracies are eliminated with integer representation.

## Working with Money Values

When dealing with money values in CoreShop, it's essential to consider that these values are stored as integers. For
instance, a value of "€10.50" would be stored as 1050 (assuming a base unit of 100 for the currency). This approach
necessitates careful handling of these values in calculations, display formatting, and database operations.

### Example Usage

In CoreShop, when displaying a price or performing calculations, you should convert these integer values back to their
decimal form. This conversion is typically handled by CoreShop's internal functions and utilities.

### Further Reading

For a deeper understanding of how CoreShop handles fractional currency values and the implications for your eCommerce
application, refer to the following documentation:

- [Currency Fractions](../20_Currency_Fractions/index.md)
