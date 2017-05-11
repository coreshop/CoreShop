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

pimcore.registerNS('pimcore.plugin.coreshop.product.pricerule.item');

pimcore.plugin.coreshop.product.pricerule.item = Class.create(pimcore.plugin.coreshop.pricerules.item, {

    iconCls : 'coreshop_icon_price_rule',

    url : {
        save : '/admin/CoreShop/product_price_rules/save'
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
            }, {
                xtype: 'checkbox',
                name: 'active',
                fieldLabel: t('active'),
                checked: this.data.active
            }]
        });

        return this.settingsForm;
    },

    addVoucherCodes : function () {}
});
