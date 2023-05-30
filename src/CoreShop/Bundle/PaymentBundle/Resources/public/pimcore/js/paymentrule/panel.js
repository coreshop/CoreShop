/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.paymentrule.panel');
coreshop.paymentrule.panel = Class.create(coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_payment_rule_panel',
    storeId: 'coreshop_payment_rules',
    iconCls: 'coreshop_nav_icon_payment_provider',
    type: 'coreshop_payment_rules',

    routing: {
        add: 'coreshop_payment_rule_add',
        delete: 'coreshop_payment_rule_delete',
        get: 'coreshop_payment_rule_get',
        list: 'coreshop_payment_rule_list',
        config: 'coreshop_payment_rule_getConfig'
    },

    getItemClass: function () {
        return coreshop.paymentrule.item;
    }
});
