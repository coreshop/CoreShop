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

pimcore.registerNS('pimcore.object.classes.data.coreShopDynamicDropdown');
pimcore.object.classes.data.coreShopDynamicDropdown = Class.create(pimcore.object.classes.data.data, {
    type: 'coreShopDynamicDropdown',
    allowIndex: true,

    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: true,
        fieldcollection: true,
        localizedfield: true,
    },

    // This is for documentation purposes (and to make ide IDE happy)
    // It will be overwritten in this.initData() immediately
    datax: {
        className: null,
        folderName: null,
        methodName: null,
        onlyPublished: null,
        recursive: null,
        sortBy: null,
        width: null,
    },

    initialize: function (treeNode, initData) {
        this.type = 'coreShopDynamicDropdown';
        this.initData(initData);
        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t('coreshop_dynamic_dropdown');
    },

    getGroup: function () {
        return 'select';
    },

    getIconClass: function () {
        return 'pimcore_icon_coreShopDynamicDropdown';
    },

    getLayout: function ($super) {
        $super();

        this.classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('pimcore_admin_dataobject_class_gettree'),
            },
            fields: ['name', 'id'],
            autoLoad: true,
        });

        this.classesCombo = new Ext.form.ComboBox({
            fieldLabel: t('coreshop_dynamic_dropdown_allowed_classes'),
            name: 'className',
            listWidth: 'auto',
            triggerAction: 'all',
            editable: false,
            store: this.classesStore,
            displayField: 'text',
            valueField: 'text',
            summaryDisplay: true,
            value: this.datax.className,

            listeners: {
                collapse: {
                    fn: function (combo/*, value*/) {
                        this.methodsCombo.store.reload({
                            params: { className: this.classesCombo.getValue() },
                        });
                        this.methodsCombo.setValue('');
                    }.bind(this),
                },
            },
        });

        this.methodsStore = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_dynamic_dropdown_methods'),
                extraParams: {
                    className: this.classesCombo.getValue()
                },
            },
            fields: ['key', 'value'],
        });

        this.methodsStore.load();

        this.methodsCombo = new Ext.form.ComboBox({
            fieldLabel: t('coreshop_dynamic_dropdown_methodname'),
            name: 'methodName',
            listWidth: 'auto',
            triggerAction: 'all',
            editable: false,
            store: this.methodsStore,
            displayField: 'value',
            valueField: 'key',
            summaryDisplay: true,
            queryMode: 'local',
            value: this.datax.methodName,
        });

        this.specificPanel.removeAll();
        this.specificPanel.add([
            {
                xtype: 'numberfield',
                fieldLabel: t('width'),
                name: 'width',
                value: this.datax.width,
            },
            {
                xtype: 'textfield',
                fieldLabel: t('coreshop_dynamic_dropdown_folder_name'),
                name: 'folderName',
                cls: 'input_drop_target',
                value: this.datax.folderName,
                listeners: {
                    render: function (el) {
                        new Ext.dd.DropZone(el.getEl(), {
                            reference: this,
                            ddGroup: 'element',
                            getTargetFromEvent: function (/* e */) {
                                return this.getEl();
                            }.bind(el),

                            onNodeOver: function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.type === 'folder') {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                }

                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            },

                            onNodeDrop: function (target, dd, e, data) {
                                data = data.records[0].data;

                                if (data.type === 'folder') {
                                    this.setValue(data.path);

                                    return true;
                                }

                                return false;
                            }.bind(el),
                        });
                    },
                },
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('coreshop_dynamic_dropdown_recursive'),
                name: 'recursive',
                checked: this.datax.recursive,
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('coreshop_dynamic_dropdown_only_published'),
                name: 'onlyPublished',
                checked: this.datax.onlyPublished,
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_dynamic_dropdown_sort_by'),
                name: 'sortBy',
                listWidth: 'auto',
                triggerAction: 'all',
                editable: false,
                value: this.datax.sortBy ? this.datax.sortBy : 'byid',
                store: [['byid', t('id')], ['byvalue', t('value')]],
            },
            this.classesCombo,
            this.methodsCombo,
        ]);

        return this.layout;
    },

    isValid: function ($super) {
        var data = this.getData();

        if (data.className === '' || data.methodName === '' || data.folderName === '') {
            return false;
        }

        return $super();
    },
});
