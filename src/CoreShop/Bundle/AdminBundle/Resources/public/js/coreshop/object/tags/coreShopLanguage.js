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

pimcore.registerNS('pimcore.object.tags.coreShopLanguage');
pimcore.object.tags.coreShopLanguage = Class.create(pimcore.object.tags.select, {

    allowEmpty : false,

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;
        this.fieldConfig.width = 350;
    },

    getLayoutEdit: function () {
        var store = new Ext.data.JsonStore({
            autoDestroy: true,
            proxy : new Ext.data.HttpProxy({
                url : '/admin/coreshop/helper/get-languages',
                reader: {
                    rootProperty: 'languages'
                }
            }),
            fields: ['language', 'display']
        });

        var options = {
            name: this.fieldConfig.name,
            triggerAction: 'all',
            editable: false,
            typeAhead: false,
            forceSelection: true,
            fieldLabel: this.fieldConfig.title,
            store: store,
            componentCls: 'object_field',
            width: 250,
            labelWidth: 100,
            displayField:'display',
            valueField:'language',
            queryMode : 'local',
            value:this.data ? this.data : null,
            listeners : {
                beforerender : function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();
                }
            }
        };

        if (this.fieldConfig.labelWidth) {
            options.labelWidth = this.fieldConfig.labelWidth;
        }

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }

        options.width += options.labelWidth;

        this.component = new Ext.form.ComboBox(options);

        return this.component;
    }
});
