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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.conditions.payment');

pimcore.plugin.coreshop.mail.rules.conditions.payment = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'payment',

    getForm : function () {
        var paymentProvidersStore = new Ext.data.Store({
            proxy : {
                type : 'ajax',
                url : '/plugin/CoreShop/admin_order/get-payment-providers',
                reader : {
                    type : 'json',
                    rootProperty : 'data'
                }
            },
            fields : ['id', 'name']
        });
        paymentProvidersStore.load();

        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                {
                    xtype:'combo',
                    fieldLabel:t('coreshop_paymentProvider'),
                    typeAhead:true,
                    mode:'local',
                    listWidth:100,
                    store:paymentProvidersStore,
                    displayField:'name',
                    valueField:'id',
                    forceSelection:true,
                    triggerAction:'all',
                    name:'provider',
                    afterLabelTextTpl: [
                        '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                    ],
                    allowBlank: false,
                    multiselect : true
                }
            ]
        });

        return this.form;
    }
});
