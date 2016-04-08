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

pimcore.registerNS('pimcore.plugin.coreshop.states.panel');
pimcore.plugin.coreshop.states.panel = Class.create(pimcore.plugin.coreshop.abstract.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_states_panel',
    storeId : 'coreshop_states',
    iconCls : 'coreshop_icon_state',
    type : 'states',

    url : {
        add : '/plugin/CoreShop/admin_State/add',
        delete : '/plugin/CoreShop/admin_State/delete',
        get : '/plugin/CoreShop/admin_State/get',
        list : '/plugin/CoreShop/admin_State/list'
    }
});
