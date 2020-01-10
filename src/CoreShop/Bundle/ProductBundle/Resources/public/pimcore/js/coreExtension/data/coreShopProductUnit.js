/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopProductUnit');
pimcore.object.classes.data.coreShopProductUnit = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopProductUnit',

    getTypeName: function () {
        return t('coreshop_product_unit');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_product_unit';
    }
});
