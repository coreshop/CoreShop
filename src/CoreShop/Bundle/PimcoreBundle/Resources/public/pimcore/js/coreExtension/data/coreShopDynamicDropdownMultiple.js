/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopDynamicDropdownMultiple');
pimcore.object.classes.data.coreShopDynamicDropdownMultiple = Class.create(pimcore.object.classes.data.coreShopDynamicDropdown, {
    type: 'coreShopDynamicDropdownMultiple',

    initialize: function (treeNode, initData) {
        this.type = 'coreShopDynamicDropdownMultiple';
        this.initData(initData);
        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t('coreshop_dynamic_dropdown_multiple');
    },

    getIconClass: function () {
        return 'pimcore_icon_coreShopDynamicDropdownMultiple';
    }
});
