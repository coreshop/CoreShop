# Architecture

CoreShop is an open-source (GPL license) eCommerce solution, maintained and financed by CoreShop GmbH. It benefits from
a diverse community of developers and companies contributing to its development.

## Core Values and Distinctions

CoreShop stands out for its:

- **Components-Based Approach**: Offering modularity and flexibility.
- **Unlimited Flexibility and Customization**: Easily tailored to specific needs.
- **Developer-Friendly**: Incorporates the latest technologies.
- **High-Quality Code**: Focus on robust and reliable coding practices.

# The Three Natures of CoreShop

CoreShop combines three distinct elements:

1. **Decoupled eCommerce Components**: Written in PHP for flexibility.
2. **Symfony Bundles**: Integrating components into the full-stack Symfony framework.
3. **Complete eCommerce Suite**: A cohesive solution crafted from these building blocks.

CoreShop can be utilized in various ways: as standalone components in any framework, as bundles in a new or existing
Pimcore application, or as a full-stack suite.

# CoreShop Suite

This documentation focuses on the CoreShop full-stack eCommerce suite. As a standard Pimcore Bundle, it provides the
foundation for common webshop functionality and custom systems.

# Leveraging Symfony Bundles

For custom systems built from scratch, CoreShop's standalone Symfony bundles can be integrated into your project.
Installation instructions are available in each bundle's documentation.

## Difference to the Official Pimcore eCommerce Framework

While the Pimcore eCommerce Framework offers a basic toolset for eCommerce development, CoreShop extends this with a
feature-rich set of tools for complex and rich eCommerce solutions.

> **Example I:** The Framework does not fully support complex shipping price calculations. This requires custom coding.
> CoreShop, however, includes Carriers and Shipping Rules for such functionality.

CoreShop's bundles are designed for individual use, with the CoreBundle combining all features for a comprehensive
solution.

# Architecture Overview

CoreShop Suite is built upon CoreShop Components and Bundles. The CoreBundle and CoreComponent unify these elements into
a cohesive eCommerce Suite for both B2B and B2C solutions.

![Architecture](img/architecture.png)
