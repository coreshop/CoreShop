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

pimcore.registerNS('pimcore.plugin.coreshop.orders.address');
pimcore.plugin.coreshop.orders.address = Class.create({

    data : [],
    objectLayout : {},
    orderId : null,
    type : null,
    cb : null,
    title : null,

    initialize: function(data, layout, orderId, type, title, cb) {
        this.data = data;
        this.objectLayout = layout;
        this.orderId = orderId;
        this.type = type;
        this.cb = cb;
        this.dataFields = {};
        this.title = title;
    },

    show : function() {
        var layout = this.getLayout(this.objectLayout);
        layout.region = 'center';

        var window = new Ext.window.Window({
            width: 500,
            height: 700,
            resizeable: true,
            modal : true,
            layout: 'border',
            title : t('coreshop_edit_address') + ': ' + this.title,
            items : layout,
            buttons: [
                {
                    text: t('save'),
                    handler: function (btn) {
                        var params = this.getValues(true);

                        if(params) {
                            params['id'] = this.orderId;
                            params['type'] = this.type;

                            window.setLoading(t('loading'));

                            Ext.Ajax.request({
                                url: '/plugin/CoreShop/admin_order/change-address',
                                params: params,
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);

                                    if (res.success) {
                                        pimcore.helpers.showNotification(t('success'), t('success'), 'success');
                                    } else {
                                        pimcore.helpers.showNotification(t('error'), t(res.message), 'error');
                                    }

                                    if(Ext.isFunction(this.cb)) {
                                        this.cb(res.success);
                                    }

                                    window.setLoading(false);
                                }.bind(this)
                            });
                        }
                    }.bind(this),
                    iconCls: 'pimcore_icon_apply'
                }
            ]
        });

        window.show();

        return window;
    },

    getLayout: function (conf) {

        if (this.layout == null) {
            var items = [];
            if (conf) {
                items = this.getRecursiveLayout(conf).items;
            }

            this.layout = Ext.create('Ext.panel.Panel', {
                items: items,
                listeners: {
                    afterrender: function () {
                        pimcore.layout.refresh();
                    }
                }
            });
        }

        return this.layout;
    },

    getDataForField: function (fieldConfig) {
        var name = fieldConfig.name;
        return this.data[name];
    },

    getMetaDataForField: function (fieldConfig) {
        return false;
    },

    addToDataFields: function (field, name) {
        if(this.dataFields[name]) {
            // this is especially for localized fields which get aggregated here into one field definition
            // in the case that there are more than one localized fields in the class definition
            // see also Object_Class::extractDataDefinitions();
            if(typeof this.dataFields[name]["addReferencedField"]){
                this.dataFields[name].addReferencedField(field);
            }
        } else {
            this.dataFields[name] = field;
        }
    },

    getValues: function (omitMandatoryCheck) {

        if (!this.layout.rendered) {
            throw "edit not available";
        }

        var dataKeys = Object.keys(this.dataFields);
        var values = {};
        var currentField;
        var invalidMandatoryFields = [];
        var isInvalidMandatory;

        for (var i = 0; i < dataKeys.length; i++) {

            try {
                if (this.dataFields[dataKeys[i]] && typeof this.dataFields[dataKeys[i]] == "object") {
                    currentField = this.dataFields[dataKeys[i]];
                    if(currentField.isMandatory() == true) {
                        isInvalidMandatory = currentField.isInvalidMandatory();
                        if (isInvalidMandatory != false) {

                            // some fields can return their own error messages like fieldcollections, ...
                            if(typeof isInvalidMandatory == "object") {
                                invalidMandatoryFields = array_merge(isInvalidMandatory, invalidMandatoryFields);
                            } else {
                                invalidMandatoryFields.push(currentField.getTitle() + " ("
                                    + currentField.getName() + ")");
                            }
                        }
                    }

                    //only include changed values in save response.
                    if(currentField.isDirty()) {
                        values[currentField.getName()] =  currentField.getValue();
                    }
                }
            }
            catch (e) {
                console.log(e);
                values[currentField.getName()] = "";
            }
        }

        if (invalidMandatoryFields.length > 0 && !omitMandatoryCheck) {
            Ext.MessageBox.alert(t("error"), t("mandatory_field_empty") + "<br />- "
                + invalidMandatoryFields.join("<br />- "));
            return false;
        }

        return values;
    }
});


pimcore.plugin.coreshop.orders.address.addMethods(pimcore.object.helpers.edit);