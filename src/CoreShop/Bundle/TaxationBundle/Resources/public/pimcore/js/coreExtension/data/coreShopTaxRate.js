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

pimcore.registerNS('pimcore.object.classes.data.coreShopTaxRate');
pimcore.object.classes.data.coreShopTaxRate = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopTaxRate',

    getTypeName: function () {
        return t('coreshop_tax_rate');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_tax_rate';
    }
});
