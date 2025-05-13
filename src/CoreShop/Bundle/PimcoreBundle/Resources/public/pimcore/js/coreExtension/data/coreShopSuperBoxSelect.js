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

pimcore.registerNS('pimcore.object.classes.data.coreShopSuperBoxSelect');
pimcore.object.classes.data.coreShopSuperBoxSelect = Class.create(pimcore.object.classes.data.coreShopDynamicDropdown, {
    type: 'coreShopSuperBoxSelect',

    initialize: function (treeNode, initData) {
        this.type = 'coreShopSuperBoxSelect';
        this.initData(initData);
        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t('coreshop_dynamic_dropdown_super_box_select');
    },

    getIconClass: function () {
        return 'pimcore_icon_coreShopSuperBoxSelect';
    }
});
