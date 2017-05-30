/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

pimcore.registerNS('pimcore.plugin.coreshop.messaging.threadstate.panel');
pimcore.plugin.coreshop.messaging.threadstate.panel = Class.create(pimcore.plugin.coreshop.messaging.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_messaging_thread_state_panel',
    storeId : 'coreshop_messaging_thread_states',
    iconCls : 'coreshop_icon_messaging_thread_state',
    type : 'threadstate',

    url : {
        add : '/admin/coreshop/messaging-thread-state/add',
        delete : '/admin/coreshop/messaging-thread-state/delete',
        get : '/admin/coreshop/messaging-thread-state/get',
        list : '/admin/coreshop/messaging-thread-state/list'
    }
});
