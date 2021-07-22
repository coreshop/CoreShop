/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
