/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopShopMultiselect');
pimcore.object.classes.data.coreShopShopMultiselect = Class.create(pimcore.plugin.coreshop.object.classes.data.dataMultiselect, {

    type: 'coreShopShopMultiselect',

    getTypeName: function () {
        return t('coreshop_stores_multiselect');
    },

    getIconClass: function () {
        return 'coreshop_icon_shop';
    },

    getGroup: function () {
        return 'coreshop';
    }
});
