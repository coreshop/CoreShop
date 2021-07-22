/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.index.panel');

coreshop.index.panel = Class.create(coreshop.resource.panel, {

    layoutId: 'coreshop_indexes_panel',
    storeId: 'coreshop_indexes',
    iconCls: 'coreshop_icon_indexes',
    type: 'coreshop_indexes',

    routing: {
        add: 'coreshop_index_add',
        delete: 'coreshop_index_delete',
        get: 'coreshop_index_get',
        list: 'coreshop_index_list',
        config: 'coreshop_index_getConfig',
        types: 'coreshop_index_getTypes'
    },

    typesStore: null,

    /**
     * constructor
     */
    initialize: function () {
        this.getConfig();

        this.panels = [];
    },

    getConfig: function () {
        var modelName = 'coreshop.model.index.interpreter';

        if (!Ext.ClassManager.get(modelName)) {
            Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    fields: ['type', 'name', 'localized', 'relation'],
                    idProperty: 'type'
                }
            );
        }

        this.getterStore = new Ext.data.JsonStore({
            data: []
        });

        this.interpreterStore = new Ext.data.JsonStore({
            data: [],
            model: modelName
        });

        this.fieldTypeStore = new Ext.data.JsonStore({
            data: []
        });

        this.classes = new Ext.data.JsonStore({
            data: []
        });

        pimcore.globalmanager.add('coreshop_index_getters', this.getterStore);
        pimcore.globalmanager.add('coreshop_index_interpreters', this.interpreterStore);
        pimcore.globalmanager.add('coreshop_index_classes', this.classes);
        pimcore.globalmanager.add('coreshop_index_field_types', this.fieldTypeStore);

        Ext.Ajax.request({
            url: Routing.generate(this.routing.config),
            method: 'get',
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);

                    this.getterStore.loadData(res.getters);
                    this.interpreterStore.loadData(res.interpreters);
                    this.fieldTypeStore.loadData(res.fieldTypes);
                    this.classes.loadData(res.classes);

                    // create layout
                    this.getLayout();
                } catch (e) {
                    //pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                }
            }.bind(this)
        });
    },

    getItemClass: function () {
        return coreshop.index.item;
    }
});
