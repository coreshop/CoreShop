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


pimcore.registerNS("pimcore.object.classes.data.coreShopCountryMultiselect");
pimcore.object.classes.data.coreShopCountryMultiselect = Class.create(pimcore.object.classes.data.multiselect, {

    type: "coreShopCountryMultiselect",
    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: true,
        fieldcollection: true,
        localizedfield: true
    },

    initialize: function (treeNode, initData) {
        this.type = "coreShopCountryMultiselect";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_country_multiselect");
    },

    getIconClass: function () {
        return "coreshop_icon_country";
    },

    getGroup: function () {
        return "coreshop";
    },

    getLayout: function ($super) {

        $super();

        this.specificPanel.removeAll();
        this.specificPanel.add([
            {
                xtype: "spinnerfield",
                fieldLabel: t("width"),
                name: "width",
                value: this.datax.width
            },
            {
                xtype: "spinnerfield",
                fieldLabel: t("height"),
                name: "height",
                value: this.datax.height
            }
        ]);

        var countryProxy = new Ext.data.HttpProxy({
            url:'/plugin/CoreShop/admin_country/get-countries'
        });
        var countryReader = new Ext.data.JsonReader({
            fields: [
                {name:'id'},
                {name:'text'}
            ]
        });

        var countryStore = new Ext.data.Store({
            proxy:countryProxy,
            reader:countryReader,
            listeners: {
                load: function() {
                    if (this.datax.restrictTo) {
                        this.possibleOptions.setValue(this.datax.restrictTo);
                    }
                }.bind(this)
            }
        });

        var options = {
            name: "restrictTo",
            triggerAction: "all",
            editable: false,
            fieldLabel: t("restrict_selection_to"),
            store: countryStore,
            itemCls: "object_field",
            height: 200,
            width: 300,
            valueField: 'id',
            displayField: 'text'
        };
        if (this.isInCustomLayoutEditor()) {
            options.disabled = true;
        }

        this.possibleOptions = new Ext.ux.form.MultiSelect(options);

        this.specificPanel.add(this.possibleOptions);
        countryStore.load();



        return this.layout;
    },

    applyData: function ($super) {
        $super();
        delete this.datax.options;
    },

    applySpecialData: function(source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax =  {};
            }
            Ext.apply(this.datax,
                {
                    restrictTo: source.datax.restrictTo
                });
        }
    }

});
