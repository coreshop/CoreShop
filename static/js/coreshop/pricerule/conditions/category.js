/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.category");
pimcore.plugin.coreshop.pricerule.conditions.category = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'category',

    getForm : function() {
        var category = {
            fieldLabel: t("coreshop_condition_category_category"),
            name: "category",
            cls: "input_drop_target",
            width: 300,
            xtype: "textfield",
            listeners: {
                "render": function (el) {
                    new Ext.dd.DropZone(el.getEl(), {
                        reference: this,
                        ddGroup: "element",
                        getTargetFromEvent: function(e) {
                            return this.getEl();
                        }.bind(el),

                        onNodeOver : function(target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == "object" && data.className == "CoreShopCategory") {
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }
                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                        },

                        onNodeDrop : function (target, dd, e, data) {
                            data = data.records[0].data;

                            if (data.elementType == "object" && data.className == "CoreShopCategory") {
                                this.setValue(data.path);
                                return true;
                            }
                            return false;
                        }.bind(el)
                    });
                }
            }
        };

        if(this.data && this.data.category) {
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