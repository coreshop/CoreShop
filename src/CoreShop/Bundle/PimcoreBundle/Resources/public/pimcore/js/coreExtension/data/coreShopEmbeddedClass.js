/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.object.classes.data.coreShopEmbeddedClass");
pimcore.object.classes.data.coreShopEmbeddedClass = Class.create(pimcore.object.classes.data.data, {
    type: "coreShopEmbeddedClass",

    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: false,
        fieldcollection: false,
        localizedfield: false,
        classificationstore: false,
        block: true
    },

    initialize: function (treeNode, initData) {
        this.type = "coreShopEmbeddedClass";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_embedded_class");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "coreshop_icon_embedded_class";
    },

    getLayout: function ($super) {
        $super();

        this.embeddedClassLayoutStore = this.getEmbeddedClassLayoutStore();
        this.embeddedClassCombo = this.getEmbeddedClassCombo();
        this.embeddedClassLayoutCombo = this.getEmbeddedClassLayoutCombo();

        this.specificPanel.removeAll();
        this.specificPanel.add([
            {
                xtype: "numberfield",
                fieldLabel: t("maximum_items"),
                name: "maxItems",
                value: this.datax.maxItems,
                disabled: this.isInCustomLayoutEditor(),
                minValue: 0
            },
            /*{
                xtype: "checkbox",
                fieldLabel: t("lazy_loading"),
                name: "lazyLoading",
                checked: this.datax.lazyLoading,
                disabled: this.isInCustomLayoutEditor()
            },
            {
                xtype: "displayfield",
                hideLabel: true,
                value: t('lazy_loading_description'),
                cls: "pimcore_extra_label_bottom",
                style: "padding-bottom:0;"
            },
            {
                xtype: "displayfield",
                hideLabel: true,
                value: t('lazy_loading_warning'),
                cls: "pimcore_extra_label_bottom",
                style: "color:red; font-weight: bold;"
            }*/
        ]);

        this.specificPanel.add([
            this.embeddedClassCombo,
            this.embeddedClassLayoutCombo
        ]);

        if (this.datax.embeddedClassName) {
            this.embeddedClassLayoutStore.load({
                className: this.datax.embeddedClassName
            });
        }

        return this.layout;
    },

    getEmbeddedClassCombo: function () {
        return Ext.create('Ext.form.ComboBox', {
            allowBlank: false,
            minWidth: 500,
            typeAhead: true,
            triggerAction: 'all',
            store: pimcore.globalmanager.get('object_types_store'),
            valueField: 'text',
            editable: true,
            queryMode: 'local',
            mode: 'local',
            anyMatch: true,
            displayField: 'text',
            fieldLabel: t('coreshop_embedded_class_name'),
            name: 'embeddedClassName',
            value: this.datax.embeddedClassName,
            forceSelection: true,
            listeners: {
                select: function (combo, record, index) {
                    this.datax.embeddedClassName = record.data.text;

                    if (this.datax.embeddedClassName) {
                        this.embeddedClassLayoutCombo.clearValue();
                        this.embeddedClassLayoutCombo.store.load({
                            params: {
                                className: this.datax.embeddedClassName
                            },
                            callback: function () {
                                this.embeddedClassLayoutCombo.setDisabled(false);

                            }.bind(this)
                        });
                    } else {
                        this.embeddedClassLayoutCombo.setDisabled(true);
                    }
                }.bind(this)
            }
        });
    },

    getEmbeddedClassLayoutStore: function () {
        return new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_pimcore_embedded_class_get_custom_layouts'),
                extraParams: {
                    className: this.datax.embeddedClassName
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['key', 'label'],
            autoLoad: false
        });
    },

    getEmbeddedClassLayoutCombo: function () {
        return Ext.create('Ext.form.ComboBox', {
            allowBlank: false,
            minWidth: 500,
            typeAhead: true,
            triggerAction: 'all',
            store: this.birdgeClassLayoutStore,
            valueField: 'text',
            editable: true,
            queryMode: 'local',
            mode: 'local',
            anyMatch: true,
            displayField: 'text',
            fieldLabel: t('coreshop_object_embedded_class_layout'),
            name: 'embeddedClassLayout',
            value: this.datax.embeddedClassLayout,
            forceSelection: true,
            disabled: true,
            listeners: {
                load: function () {
                    this.embeddedClassLayoutCombo.setDisabled(false);
                }.bind(this)
            }
        });
    },

    applySpecialData: function (source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax = {};
            }

            Ext.apply(this.datax, {
                maxItems: source.datax.maxItems,
                embeddedClassName: source.datax.embeddedClassName,
                embeddedClassLayout: source.datax.embeddedClassLayout,
                lazyLoading: source.datax.lazyLoading
            });
        }
    }
});
