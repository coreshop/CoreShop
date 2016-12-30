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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.panel');

pimcore.plugin.coreshop.mail.rules.panel = Class.create(pimcore.plugin.coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_mail_rule_panel',
    storeId : 'coreshop_mail_rules',
    iconCls : 'coreshop_icon_mail_rule',
    type : 'mail_rule',

    url : {
        add : '/plugin/CoreShop/admin_mail-rule/add',
        delete : '/plugin/CoreShop/admin_mail-rule/delete',
        get : '/plugin/CoreShop/admin_mail-rule/get',
        list : '/plugin/CoreShop/admin_mail-rule/list',
        config : '/plugin/CoreShop/admin_mail-rule/get-config'
    },

    getItemClass : function () {
        return pimcore.plugin.coreshop.mail.rules.item;
    },

    getActionsForType : function(allowedType) {
        var actions = this.getActions();

        if(actions.hasOwnProperty(allowedType)) {
            return actions[allowedType];
        }

        return [];
    },

    getConditionsForType : function(allowedType) {
        var conditions = this.getConditions();
        var allowedConditions = [];

        if(conditions.hasOwnProperty(allowedType)) {
            return conditions[allowedType];
        }

        return [];
    }
});
