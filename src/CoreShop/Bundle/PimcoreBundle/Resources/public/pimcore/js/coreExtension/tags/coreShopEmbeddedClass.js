/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.object.tags.coreShopEmbeddedClass");
pimcore.object.tags.coreShopEmbeddedClass = Class.create(pimcore.object.tags.abstract, {
    type: "coreShopEmbeddedClass",
    dataFields: {},

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(null, this);

        this.objects = data ? data : [];

        if (!pimcore.globalmanager.exists('coreshop_embedded_class_layouts')) {
            pimcore.globalmanager.add('coreshop_embedded_class_layouts', {});
        }

        if (!pimcore.globalmanager.exists('coreshop_embedded_class_layouts_loading')) {
            pimcore.globalmanager.add('coreshop_embedded_class_layouts_loading', {});
        }
    },

    getGridColumnEditor: function (field) {
        return false;
    },

    getGridColumnFilter: function (field) {
        return false;
    },

    postSaveObject: function (object, task) {
        var objectIdToCheck = this.object.id;

        if (this.context.hasOwnProperty('coreShopEmbeddedClassObjectId')) {
            objectIdToCheck = this.context.coreShopEmbeddedClassObjectId;
        }

        if (object.id === objectIdToCheck && task === "publish") {
            var items = this.container.getItems();

            for (var itemIndex = 0; itemIndex < items.length; itemIndex++) {
                items[itemIndex].setIndex(itemIndex);
            }
        }
    },

    getLayoutEdit: function () {
        this.container = new coreshop.pimcore.coreExtension.embeddedClassContainer(this, false);

        this.component = this.getLayout(false);

        this.component.on("destroy", function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
        }.bind(this));

        return this.component;
    },

    getLayoutShow: function () {
        this.container = new coreshop.pimcore.coreExtension.embeddedClassContainer(this, true);

        this.component = this.getLayout(true);

        return this.component;
    },

    getLayout: function (noteditable) {
        var me = this,
            container = me.container,
            containerLayout = container.getLayout(),
            className = me.fieldConfig.embeddedClassName,
            layoutId = me.fieldConfig.embeddedClassLayout,
            cacheKey = className + (layoutId ? layoutId : '_default'),
            cacheEntry = null;

        if (pimcore.globalmanager.get('coreshop_embedded_class_layouts').hasOwnProperty(cacheKey)) {
            cacheEntry = pimcore.globalmanager.get('coreshop_embedded_class_layouts')[cacheKey];

            me.layoutLoaded(cacheEntry.layout, cacheEntry.general, noteditable);
        }
        else if (pimcore.globalmanager.get('coreshop_embedded_class_layouts_loading').hasOwnProperty(cacheKey)) {
            coreshop.broker.addListenerOnce('embedded_class.layout.loaded.' + cacheKey, function (data) {
                me.layoutLoaded(data.layout, data.general, noteditable);
            });
        }
        else {
            pimcore.globalmanager.get('coreshop_embedded_class_layouts_loading')[cacheKey] = true;

            Ext.Ajax.request({
                url: '/admin/coreshop/embedded-class/get-layout-configuration',
                params: {
                    className: className,
                    layoutId: layoutId
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    coreshop.broker.fireEvent('embedded_class.layout.loaded.' + cacheKey, data);

                    pimcore.globalmanager.get('coreshop_embedded_class_layouts')[cacheKey] = data;
                    delete pimcore.globalmanager.get('coreshop_embedded_class_layouts_loading')[cacheKey];

                    me.layoutLoaded(data.layout, data.general, noteditable);

                    containerLayout.setLoading(false);
                }.bind(this)
            });

            containerLayout.setLoading(true);
        }

        return containerLayout;
    },

    layoutLoaded: function (layout, general, noteditable) {
        var me = this;

        me.layout = layout;
        me.general = general;

        Ext.each(this.objects, function (object) {
            me.addEmbeddedClass(object, object.general, noteditable);
        });
    },

    createNew: function () {
        var me = this,
            data = {},
            general = {
                o_className: me.fieldConfig.embeddedClassName,
                index: t('new')
            },
            object = {
                general: general,
                data: data,
                metaData: {}
            };
        me.addEmbeddedClass(object, general, false);
    },

    addEmbeddedClass: function (object, general, noteditable) {
        var me = this,
            container = me.container,
            pimcoreObjectEdit,
            objectId = me.context.hasOwnProperty('coreShopEmbeddedClassObjectId') ? me.context.coreShopEmbeddedClassObjectId : me.object.id;

        pimcoreObjectEdit = new pimcore.object.edit({
            id: object.id,
            data: object,
            ignoreMandatoryFields: false
        });
        pimcoreObjectEdit.getLayout = function (conf) {
            if (this.layout == null) {
                var items = [];
                if (conf) {
                    items = this.getRecursiveLayout(conf, noteditable, {coreShopEmbeddedClassObjectId: objectId}).items;
                }

                this.layout = Ext.create('Ext.panel.Panel', {
                    bodyStyle: 'background-color: #fff;',
                    border: false,
                    //layout: 'border',
                    layout: "fit",
                    cls: "pimcore_object_panel_edit",
                    items: items,
                    listeners: {
                        afterrender: function () {
                            pimcore.layout.refresh();
                        }
                    }
                });
            }

            return this.layout;
        };

        container.add(pimcoreObjectEdit, me.layout, general, me.general.iconCls);
    },

    getValue: function () {
        if (!this.component.rendered) {
            throw 'edit not available';
        }

        var me = this,
            items = this.container.getItems(),
            values = [],
            object,
            objectValues;

        Ext.each(items, function (item) {
            object = item.objectEdit;

            if (!item.isRemoved()) {
                object.object.ignoreMandatoryFields = me.object.ignoreMandatoryFields;

                objectValues = object.getValues();

                if (object.object.data.hasOwnProperty('id')) {
                    objectValues['id'] = object.object.data.id;
                }

                objectValues['currentIndex'] = item.getCurrentIndex();

                if (item.getIndex()) {
                    objectValues['originalIndex'] = item.getIndex();
                }

                values.push(objectValues);
            }
        });

        return values;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    isInvalidMandatory: function () {
        var me = this,
            invalidMandatoryFields = [],
            isInvalidMandatory,
            layouts = me.container.getLayouts(),
            object = null,
            dataKeys = null,
            currentField = null;

        for (var i = 0; i < layouts.length; i++) {
            object = layouts[i];
            dataKeys = Object.keys(object.dataFields);

            for (var j = 0; j < dataKeys.length; j++) {
                if (object.dataFields[dataKeys[j]] && typeof object.dataFields[dataKeys[j]] === 'object') {
                    currentField = object.dataFields[dataKeys[j]];

                    if (currentField.isMandatory() === true) {
                        isInvalidMandatory = currentField.isInvalidMandatory();
                        if (isInvalidMandatory !== false) {

                            // some fields can return their own error messages like fieldcollections, ...
                            if (typeof isInvalidMandatory === 'object') {
                                invalidMandatoryFields = array_merge(isInvalidMandatory, invalidMandatoryFields);
                            } else {
                                invalidMandatoryFields.push(currentField.getTitle() + ' ('
                                    + currentField.getName() + ')');
                            }
                        }
                    }
                }
            }
        }

        return invalidMandatoryFields;
    },

    isDirty: function () {
        var me = this,
            items = me.container.getItems(),
            objects = me.container.getLayouts(),
            object = null,
            dataKeys = null,
            currentField = null,
            i,
            j;

        for (i = 0; i < items.length; i++) {
            if (items[i].isDirty()) {
                return true;
            }
        }

        for (i = 0; i < objects.length; i++) {
            object = objects[i];
            dataKeys = Object.keys(object.dataFields);

            if (!object.object.data.hasOwnProperty('id')) {
                return true;
            }

            for (j = 0; j < dataKeys.length; j++) {
                if (object.dataFields[dataKeys[j]] && typeof object.dataFields[dataKeys[j]] === 'object') {
                    currentField = object.dataFields[dataKeys[j]];

                    if (currentField.isDirty()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
});
