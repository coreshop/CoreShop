/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.object.tags.coreShopShop');
pimcore.object.tags.coreShopShop = Class.create(pimcore.plugin.coreshop.object.tags.select, {

    type: 'coreShopShop',
    storeName : 'shops',

    getLayoutEdit: function ($super) {
        var component = $super();

        if(!coreshop.settings.multishop) {
            component.hide();
        }

        return component;
    }
});
