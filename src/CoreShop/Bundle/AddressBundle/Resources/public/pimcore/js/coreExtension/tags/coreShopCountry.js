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

pimcore.registerNS('pimcore.object.tags.coreShopCountry');
pimcore.object.tags.coreShopCountry = Class.create(coreshop.object.tags.select, {

    type: 'coreShopCountry',
    storeName: 'coreshop_countries',
    getLayoutEdit: function () {
        const layout = coreshop.object.tags.select.prototype.getLayoutEdit.call(this);

        if (layout && layout.setEditable) {
            layout.setEditable(true);
        }

        return layout;
    }

});
