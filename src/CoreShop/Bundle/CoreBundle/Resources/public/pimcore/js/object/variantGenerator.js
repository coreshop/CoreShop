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

pimcore.registerNS('coreshop.object.variantGenerator');
coreshop.object.variantGenerator = Class.create({

    initialize: function (object) {
        this.object = object;
        this.allowedVariants = object.data.general.allowVariants;

        this.window = new Ext.Window({
            width: 400,
            height: 300,
            modal: true,
            iconCls: 'pimcore_icon_variant',
            title: t('coreshop_variant_generator'),
            layout: 'fit',
            items: [this.getInputPanel()]
        });

        this.window.show();

    },

    getInputPanel: function () {

        if (!this.inputPanel) {
            this.brickTypeStore = new Ext.data.JsonStore({
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/object/get-variant-bricks',
                    extraParams: {
                        id: this.object.id
                    },
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
                fields: ['name']
            });

            this.brickFieldStore = new Ext.data.JsonStore({
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/object/get-brick-fields',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
                fields: ['name', 'type']
            });

            this.brickSelector = new Ext.form.ComboBox({
                xtype: 'combo',
                name: 'brickType',
                width: 350,
                autoSelect: true,
                editable: false,
                fieldLabel: t('coreshop_variant_generator_brick'),
                store: this.brickTypeStore,
                triggerAction: 'all',
                valueField: 'name',
                displayField: 'name',
                listeners: {
                    select: function (combo, newValue, oldValue) {
                        this.brickFieldStore.load({
                            params: {
                                key: newValue.get('name')
                            },
                            callback: function () {
                                this.brickFieldSelector.setDisabled(false);
                            }.bind(this)
                        });
                    }.bind(this)
                }
            });

            this.brickFieldSelector = new Ext.form.ComboBox({
                xtype: 'combo',
                disabled: true,
                name: 'brickField',
                width: 350,
                queryMode: 'local',
                autoSelect: true,
                editable: false,
                fieldLabel: t('coreshop_variant_generator_field'),
                store: this.brickFieldStore,
                triggerAction: 'all',
                valueField: 'name',
                displayField: 'name'
            });

            this.inputValues = new Ext.form.TextField({
                name: 'input',
                fieldLabel: t('coreshop_variant_generator_input')
            });

            this.inputPanel = new Ext.form.FormPanel({
                region: 'center',
                bodyStyle: 'padding: 5px;',
                items: [this.brickSelector, this.brickFieldSelector, this.inputValues],
                buttons: [{
                    text: t('create'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function () {
                        Ext.Ajax.request({
                            url: '/admin/coreshop/object/generate-variants',
                            method: 'post',
                            params: {
                                objectId: this.object.id,
                                brickType: this.brickSelector.getValue(),
                                field: this.brickFieldSelector.getValue(),
                                values: this.inputValues.getValue()
                            },
                            success: function (response) {
                                this.window.close();
                            }.bind(this)
                        });
                    }.bind(this)
                }]
            });

        }

        return this.inputPanel;

    }

});

