/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
