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

pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.item');

pimcore.plugin.coreshop.carrier.shippingrules.item = Class.create(pimcore.plugin.coreshop.rules.item, {

    iconCls : 'coreshop_icon_carrier_shipping_rule',

    url : {
        save : '/plugin/CoreShop/admin_carrier-shipping-rule/save'
    },

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            buttons: [{
                text: t('save'),
                iconCls: 'pimcore_icon_apply',
                handler: this.save.bind(this)
            }],
            items: this.getItems()
        });

        return this.panel;
    },


    getSettings: function () {
        var data = this.data;

        this.settingsForm = Ext.create('Ext.form.Panel', {
            iconCls: 'coreshop_icon_settings',
            title: t('settings'),
            bodyStyle: 'padding:10px;',
            autoScroll: true,
            border:false,
            items: [{
                xtype: 'textfield',
                name: 'name',
                fieldLabel: t('name'),
                width: 250,
                value: data.name
            }]
        });

        return this.settingsForm;
    },

    getActionContainerClass : function() {
        return pimcore.plugin.coreshop.carrier.shippingrules.action;
    },

    getConditionContainerClass : function() {
        return pimcore.plugin.coreshop.carrier.shippingrules.condition;
    }
});
