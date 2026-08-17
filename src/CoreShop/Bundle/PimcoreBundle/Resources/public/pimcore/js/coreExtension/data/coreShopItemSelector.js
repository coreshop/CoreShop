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

pimcore.registerNS('pimcore.object.classes.data.coreShopItemSelector');
pimcore.object.classes.data.coreShopItemSelector = Class.create(pimcore.object.classes.data.coreShopDynamicDropdown, {
    type: 'coreShopItemSelector',

    initialize: function (treeNode, initData) {
        this.type = 'coreShopItemSelector';
        this.initData(initData);
        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t('coreshop_dynamic_dropdown_item_selector');
    },

    getIconClass: function () {
        return 'pimcore_icon_coreShopItemSelector';
    }
});
