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

pimcore.registerNS('pimcore.object.classes.data.coreShopStoreMultiselect');
pimcore.object.classes.data.coreShopStoreMultiselect = Class.create(coreshop.object.classes.data.dataMultiselect, {

    type: 'coreShopStoreMultiselect',

    getTypeName: function () {
        return t('coreshop_store_multiselect');
    },

    getIconClass: function () {
        return 'coreshop_icon_store';
    },

    getGroup: function () {
        return 'coreshop';
    }
});
