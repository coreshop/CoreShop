/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopPaymentProviderMultiselect');
pimcore.object.classes.data.coreShopPaymentProviderMultiselect = Class.create(coreshop.object.classes.data.dataMultiselect, {

    type: 'coreShopPaymentProviderMultiselect',

    getTypeName: function () {
        return t('coreshop_payment_provider_multiselect');
    },

    getIconClass: function () {
        return 'coreshop_icon_payment_provider';
    },

    getGroup: function () {
        return 'coreshop';
    }
});
