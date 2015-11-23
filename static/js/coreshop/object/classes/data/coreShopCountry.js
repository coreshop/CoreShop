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


pimcore.registerNS("pimcore.object.classes.data.coreShopCountry");
pimcore.object.classes.data.coreShopCountry = Class.create(pimcore.object.classes.data.data, {

    type: "coreShopCountry",
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
        this.type = "coreShopCountry";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_country");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "coreshop_icon_country";
    },

    getLayout: function ($super) {

        $super();

        this.specificPanel.removeAll();

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
            valueField: 'value',
            displayField: 'key',
            disabled: this.isInCustomLayoutEditor()
        };

        this.possibleOptions = new Ext.ux.form.MultiSelect(options);

        this.specificPanel.add(this.possibleOptions);
        countryStore.load();

        return this.layout;
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
