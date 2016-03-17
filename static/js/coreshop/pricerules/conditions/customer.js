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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.conditions.customer');

pimcore.plugin.coreshop.pricerules.conditions.customer = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'customer',

    getForm : function () {
        var customer = {
            fieldLabel: t('coreshop_condition_customer_customer'),
            name: 'customer',
            cls: 'input_drop_target',
            width: 300,
            xtype: 'textfield',
            listeners: {
                render: function (el) {
                    new Ext.dd.DropZone(el.getEl(), {
                        reference: this,
                        ddGroup: 'element',
                        getTargetFromEvent: function (e) {
                            return this.getEl();
                        }.bind(el),

                        onNodeOver : function (target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == 'object' && data.className == 'CoreShopUser') {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }

                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                        },

                        onNodeDrop : function (target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == 'object' && data.className == 'CoreShopUser') {
                                this.setValue(data.path);
                                return true;
                            }

                            return false;
                        }.bind(el)
                    });
                }
            }
        };

        if (this.data && this.data.customer) {
            customer.value = this.data.customer;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                customer
            ]
        });

        return this.form;
    }
});
