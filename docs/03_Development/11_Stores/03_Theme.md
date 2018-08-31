# CoreShop Stores Theming

CoreShop Stores are designed to allow different Themes. Therefore you can have a true Multi-Store Environment with different Themes.

## Example

Let's create a new theme called `sports`:

1. Create a store and type `sports` into the `template` field
2. Create a folder called `themes` in the `app/Resources` directory
3. Inside the `themes` directory create a new folder and name it `sports`
4. Finally add a folder called `CoreShopFrontendBundle` inside your new theme directory

Every view request inside the given store context will search for a view file in the `app/Resources/themes/sports/CoreShopFrontendBundle/*` directory.

So for example you want to have a different homepage you will need to create a file: `app/Resources/themes/sports/CoreShopFrontendBundle/index/index.html.twig`.

When the customer then visits your website on store `sports`, CoreShop will load the new index.html.twig file.