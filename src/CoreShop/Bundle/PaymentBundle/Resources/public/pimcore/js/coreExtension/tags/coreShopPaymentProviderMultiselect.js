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

pimcore.registerNS('pimcore.object.tags.coreShopPaymentProviderMultiselect');
pimcore.object.tags.coreShopPaymentProviderMultiselect = Class.create(coreshop.object.tags.multiselect, {

    type: 'coreShopPaymentProviderMultiselect',
    storeName: 'coreshop_payment_provider',
    displayField: 'identifier'
});
