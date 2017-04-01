<?php


namespace CoreShop\Bundle\CoreShopLegacyBundle;

use CoreShop\Bundle\CoreShopLegacyBundle\Tool\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;


class CoreShopLegacyBundle extends AbstractPimcoreBundle
{

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/coreshoplegacy/css/coreshop.css'
        ];
    }

    /**
     * @return array
     */
    public function getJsPaths()
    {
        return [
            '/bundles/coreshoplegacy/js/startup.js',
            '/bundles/coreshoplegacy/js/coreshop/plugin/broker.js',
            '/bundles/coreshoplegacy/js/coreshop/plugin/plugin.js',
            '/bundles/coreshoplegacy/js/coreshop/helper.js',
            '/bundles/coreshoplegacy/js/coreshop/global.js',
            '/bundles/coreshoplegacy/js/coreshop/settings.js',
            '/bundles/coreshoplegacy/js/coreshop/update.js',
            '/bundles/coreshoplegacy/js/coreshop/abstract/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/abstract/item.js',
            '/bundles/coreshoplegacy/js/coreshop/currencies/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/currencies/item.js',
            '/bundles/coreshoplegacy/js/coreshop/countries/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/countries/item.js',
            '/bundles/coreshoplegacy/js/coreshop/zones/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/zones/item.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/multiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/select.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/data.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/select.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/dataMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCountryMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCountryMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCountry.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCountry.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCarrier.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCarrier.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCarrierMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCarrierMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopPriceRule.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopPriceRule.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCurrencyMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCurrencyMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopCurrency.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopCurrency.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/item.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/action.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/condition.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/actions/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/amount.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/weight.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/quantity.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/categories.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/countries.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/conditions.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/customers.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/customerGroups.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/carriers.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/products.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/timeSpan.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/totalPerCustomer.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/zones.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/currencies.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/actions/freeShipping.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/actions/discountAmount.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/actions/discountPercent.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/actions/gift.js',
            '/bundles/coreshoplegacy/js/coreshop/pricerules/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/pricerules/item.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/item.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopOrderState.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopOrderState.js',
            '/bundles/coreshoplegacy/js/coreshop/broker.js',
            '/bundles/coreshoplegacy/js/coreshop/install/install.js',
            '/bundles/coreshoplegacy/js/coreshop/taxes/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/taxes/item.js',
            '/bundles/coreshoplegacy/js/coreshop/taxrulegroups/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/taxrulegroups/item.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopTaxRuleGroup.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopTaxRuleGroup.js',
            '/bundles/coreshoplegacy/js/coreshop/layout/portlets/ordersAndCartsFromLastDays.js',
            '/bundles/coreshoplegacy/js/coreshop/layout/portlets/salesFromLastDays.js',
            '/bundles/coreshoplegacy/js/coreshop/object/objectMultihref.js',
            '/bundles/coreshoplegacy/js/coreshop/object/elementHref.js',
            '/bundles/coreshoplegacy/js/coreshop/object/variantGenerator.js',
            '/bundles/coreshoplegacy/js/coreshop/product/specificprice/actions/newPrice.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/grid.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/item.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/fields.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/objecttype/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/type/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/type/elasticsearch.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/getters/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/getters/brick.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/getters/fieldcollection.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/getters/localizedfield.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/getters/classificationstore.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/interpreters/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/indexes/interpreters/objectproperty.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/item.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/condition.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/select.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/multiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/boolean.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/combined.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/conditions/range.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/similarity.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/similarities/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/product/filters/similarities/field.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopFilter.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopFilter.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopLanguage.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopLanguage.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/createPayment.js',
            '/bundles/coreshoplegacy/js/coreshop/report/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/sales.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/carts.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/cartsAbandoned.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/products.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/categories.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/customers.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/quantities.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/carriers.js',
            '/bundles/coreshoplegacy/js/coreshop/report/reports/payments.js',
            '/bundles/coreshoplegacy/js/coreshop/report/monitoring/abstract.js',
            '/bundles/coreshoplegacy/js/coreshop/report/monitoring/reports/emptyCategories.js',
            '/bundles/coreshoplegacy/js/coreshop/report/monitoring/reports/disabledProducts.js',
            '/bundles/coreshoplegacy/js/coreshop/states/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/states/item.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopState.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopState.js',
            '/bundles/coreshoplegacy/js/coreshop/report/monitoring/reports/outOfStockProducts.js',
            '/bundles/coreshoplegacy/js/coreshop/product/grid.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/contact/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/contact/item.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/threadstate/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/threadstate/item.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/thread/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/messaging/thread/item.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/message.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/order.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/create/order.js',
            '/bundles/coreshoplegacy/js/coreshop/product/pricerule/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/product/pricerule/item.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/address.js',
            '/bundles/coreshoplegacy/js/coreshop/shops/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/shops/item.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopShop.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopShop.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopShopMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopShopMultiselect.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/shops.js',
            '/bundles/coreshoplegacy/js/coreshop/rules/conditions/personas.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/item.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/action.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/condition.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/additionAmount.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/additionPercent.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/discountAmount.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/discountPercent.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/fixedPrice.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/actions/shippingRule.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/shippingRule.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/conditions.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/countries.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/amount.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/weight.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/zones.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/dimension.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/postcodes.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/products.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/categories.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/customerGroups.js',
            '/bundles/coreshoplegacy/js/coreshop/carriers/shippingrules/conditions/currencies.js',
            '/bundles/coreshoplegacy/js/coreshop/object/tags/coreShopSpecificPrices.js',
            '/bundles/coreshoplegacy/js/coreshop/object/classes/data/coreShopSpecificPrices.js',
            '/bundles/coreshoplegacy/js/coreshop/product/specificprice/object/item.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/invoice/render.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/shipment/render.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/invoice.js',
            '/bundles/coreshoplegacy/js/coreshop/orders/shipment.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/panel.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/item.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/action.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/condition.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/order/payment.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/order/orderState.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/order/carriers.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/invoice/invoiceState.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/shipment/shipmentState.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/user/userType.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/messaging/messageType.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/actions/mail.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/actions/orderMail.js',
            '/bundles/coreshoplegacy/js/coreshop/mail/rules/conditions/payment/paymentState.js'
        ];
    }

    /**
     *
     */
    public function boot()
    {
        parent::boot();

        CoreShop::bootstrap($this);

        require_once __DIR__ . '/helper.php';
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return new Installer();
    }
}
