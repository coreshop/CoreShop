/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.index.interpreters.iterator');

coreshop.index.interpreters.iterator = Class.create(coreshop.index.interpreters.abstract, {
    getStore: function() {
        return pimcore.globalmanager.get('coreshop_index_interpreters');
    },

    getForm: function (record, config) {
        this.interpreterPanel = new Ext.form.FormPanel({
            defaults: { anchor: '90%' },
            layout: 'form',
            title: t('coreshop_index_interpreter_settings'),
            border: true,
            hidden: true
        });

        this.getStore().clearFilter();

        this.interpreterTypeCombo = new Ext.form.ComboBox({
            fieldLabel : t('coreshop_index_field_interpreter'),
            name : 'interpreter',
            length : 255,
            value : config && config.interpreter ? config.interpreter.type : null,
            store : this.getStore(),
            valueField : 'name',
            displayField : 'name',
            queryMode : 'local',
            listeners : {
                change : function (combo, newValue) {
                    this.interpreterPanel.removeAll();

                    this.getInterpreterPanelLayout(newValue, record, config, {});
                }.bind(this)
            }
        });

        this.interpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            items: [
                this.interpreterTypeCombo,
                this.interpreterPanel
            ],
            border: false
        });

        if (config && config.interpreter && config.interpreter.type) {
            this.getInterpreterPanelLayout(config.interpreter.type, record, config, config.interpreter.interpreterConfig);
        }

        return this.interpreterContainer;
    },

    destroy: function () {
        if (this.interpreterContainer) {
            this.interpreterContainer.destroy();
        }
    },

    getInterpreterPanelLayout : function (type, record, parentConfig, config) {
        if (type) {
            if (coreshop.index.interpreters[type]) {
                this.interpreterPanelClass = new coreshop.index.interpreters[type];

                this.interpreterPanel.add(this.interpreterPanelClass.getForm(record, Ext.isObject(config) ? config : {}, parentConfig));
                this.interpreterPanel.show();
            } else {
                this.interpreterPanel.hide();

                this.interpreterPanelClass = null;
            }
        } else {
            this.interpreterPanel.hide();
        }
    },

    isValid: function() {
        if (!this.interpreterPanelClass) {
            return this.interpreterTypeCombo.getValue() ? true : false;
        }

        return this.interpreterPanelClass.isValid();
    },

    getInterpreterData: function () {
        // get defined conditions
        if (this.interpreterPanelClass) {
            var interpreterConfig  = {};
            var interpreterForm = this.interpreterPanel.getForm();

            if (Ext.isFunction(this.interpreterPanelClass.getInterpreterData)) {
                interpreterConfig = this.interpreterPanelClass.getInterpreterData();
            }
            else {
                Ext.Object.each(interpreterForm.getFieldValues(), function (key, value) {
                    interpreterConfig[key] = value;
                }.bind(this));
            }

            return {
                interpreter: {
                    interpreterConfig: interpreterConfig,
                    type: this.interpreterTypeCombo.getValue()
                }
            };
        }

        return {
            interpreter: {
                type: this.interpreterTypeCombo.getValue()
            }
        };
    }
});
