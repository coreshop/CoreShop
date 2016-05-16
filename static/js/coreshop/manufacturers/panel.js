/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.manufacturers.panel');

pimcore.plugin.coreshop.manufacturers.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_manufacturers_panel',
    storeId : 'coreshop_manufacturers',
    iconCls : 'coreshop_icon_manufacturers',
    type : 'manufacturers',

    url : {
        add : '/plugin/CoreShop/admin_manufacturer/add',
        delete : '/plugin/CoreShop/admin_manufacturer/delete',
        get : '/plugin/CoreShop/admin_manufacturer/get',
        list : '/plugin/CoreShop/admin_manufacturer/list'
    }
});
