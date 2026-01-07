## 4.1.7
* [CoreBundle] fix store values version preview with null values by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2912
* [GraphQL] enable all translations for graphql by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2914

## 4.1.6
* [IndexBundle] Ignore missing 404 error when deleting non-existent document by @aarongerig in https://github.com/coreshop/CoreShop/pull/2896
* [PimcoreBundle] replace outdated translation ts with t by @philippmunz in https://github.com/coreshop/CoreShop/pull/2905

## 4.1.5
* [IndexBundle] Optimize 404 Exception Handling on Index Item Deletion by @aarongerig in https://github.com/coreshop/CoreShop/pull/2894

## 4.1.4
* [Pimcore] DynamicDropdown - check if null by @codingioanniskrikos in https://github.com/coreshop/CoreShop/pull/2874
* [Messenger] Fix `MessageRepository` service registration by @jdreesen in https://github.com/coreshop/CoreShop/pull/2876
* [UPMERGE] 3.2 -> 4.0 by @github-actions[bot] in https://github.com/coreshop/CoreShop/pull/2878
* [UPMERGE] 4.0 -> 4.1 by @github-actions[bot] in https://github.com/coreshop/CoreShop/pull/2879
* [UPMERGE] 4.0 -> 4.1 by @github-actions[bot] in https://github.com/coreshop/CoreShop/pull/2875
* [Index] CoreShop Index Command with Opensearch by @aarongerig in https://github.com/coreshop/CoreShop/pull/2880
* [Resource Bundle] Add specific settings for Multiselect Datatype by @aarongerig in https://github.com/coreshop/CoreShop/pull/2881

## 4.1.3
* [MoneyBundle] add MutationType for coreShopMoney by @breakone in https://github.com/coreshop/CoreShop/pull/2836
* [CoreBundle] fix backend-order-creation carrier selection by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2839
* [Composer] update dev dependencies by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2841
* [OrderBundle] make order-creation action column wider to see delete button by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2842
* Bugfix/Light Theme Icon by @torqdev in https://github.com/coreshop/CoreShop/pull/2838
* [CS] Refactor by @github-actions in https://github.com/coreshop/CoreShop/pull/2835
* Added cart events for modifying items in the cart by @aashan10 in https://github.com/coreshop/CoreShop/pull/2853
* [Messenger] wrap message details info modal data in `<pre>` tags by @jdreesen in https://github.com/coreshop/CoreShop/pull/2855
* [Messenger] dispatch `MessageDetailsEvent` to allow customization of message details generation by @jdreesen in https://github.com/coreshop/CoreShop/pull/2854
* [CORS-49] - CoreShop Improvements by @codingioanniskrikos in https://github.com/coreshop/CoreShop/pull/2845
* [Core] show "hideFromCheckout" Carriers in Backend Order creation by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2856
* [IndexBundle] Add OpenSearch Index Worker by @aarongerig in https://github.com/coreshop/CoreShop/pull/2840

## 4.1.2
* [Docs] update payum link by @cngJo in https://github.com/coreshop/CoreShop/pull/2819
* [FrontendBundle] install css/js (public) files too by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2818
* [IndexBundle] add feature to automatically create migrations for MySQL Indices by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2827

## 4.1.1
* [Index] add feature to raw query the index by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2795
* [StorageList] fix writing null into session by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2794
* Do not load complete product again from database on pre_update by @BlackbitDevs in https://github.com/coreshop/CoreShop/pull/2781

## 4.1.0
* [Registry] require registry 4.1 in all bundles by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2748
* Update CartController - remove Argument Injection by @steinerCors in https://github.com/coreshop/CoreShop/pull/2741
* [Frontend] introduce template installer and better define best-practice by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2739

## 4.1.0-RC2

> **Important** The FrontendBundle is now disabled by default. We added a migration to enable it
> Please check if it actually is enabled in the bundles.php file
> If you don't need it, feel free to disable it.
* [ResourceBundle] check also for empty "pimcore_class_name" by @breakone in https://github.com/coreshop/CoreShop/pull/2716
* [CoreBundle] implement name functions and add migration for order-name and wishlist-name by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2717
* [Pimcore] introduce the Printable by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2720
* [Printable] further improvements to new printable feature by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2724

## 4.1.0-RC1 

> **Important**: Dependency to dachcom-digital/emailizr has been removed due to licensing issues with GPL and CCL. If
> you are using the emailzr extension, please install it manually again with
> composer require dachcom-digital/emailizr

* [Attributes] allow PHP8 Attributes for tagging services by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2533
* [TestBundle] introduce a standalone test-bundle to make testing with Pimcore and Behat easier by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2078
* [Core] add tax-rule per store by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2341
* [ResourceBundle] auto registration of pimcore models by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2536
* [Payment] allow encryption of gatway configs by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2538
* [Order] allow passing custom-attributes from price rules to order-item by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2555
* [StorageList] Multi Cart Selection by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2631
* [JMS] allow v5 by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2493
* [OrderBundle] re-factor PDF rendering to use Pimcore Web2Print by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2668
* [Emailzr] remove extension by @dpfaffenbauer in https://github.com/coreshop/CoreShop/pull/2703