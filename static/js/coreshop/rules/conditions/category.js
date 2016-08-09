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

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.category');

pimcore.plugin.coreshop.rules.conditions.category = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'category',

    getForm : function () {
        var category = {
            fieldLabel: t('coreshop_condition_category_category'),
            name: 'category',
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

                            if (data.elementType == 'object' && data.className == 'CoreShopCategory') {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }

                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                        },

                        onNodeDrop : function (target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == 'object' && data.className == 'CoreShopCategory') {
                                this.setValue(data.id);
                                return true;
                            }

                            return false;
                        }.bind(el)
                    });
                }
            }
        };

        if (this.data && this.data.category) {
            category.value = this.data.category;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                category
            ],
            border : 0
        });

        return this.form;
    }
});
