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

pimcore.registerNS("pimcore.plugin.coreshop.pricerules.conditions.customerGroup");

pimcore.plugin.coreshop.pricerules.conditions.customerGroup = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'customerGroup',

    getForm : function() {
        var me = this;

        var customerGroup = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_customerGroup'),
            typeAhead: true,
            queryMode: 'local',
            listWidth: 100,
            width : 200,
            store: pimcore.globalmanager.get("coreshop_customer_groups"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'customerGroup',
            listeners: {
                beforerender: function () {
                    if(me.data && me.data.customerGroup)
                        this.setValue(me.data.customerGroup);
                }
            }
        };

        if(this.data && this.data.customerGroup) {
            customerGroup.value = this.data.customerGroup;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                customerGroup
            ]
        });

        return this.form;
    }
});