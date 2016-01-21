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


pimcore.registerNS("pimcore.object.classes.data.coreShopCustomerGroupMultiselect");
pimcore.object.classes.data.coreShopCustomerGroupMultiselect = Class.create(pimcore.object.classes.data.multiselect, {

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
        this.type = "coreShopCustomerGroupMultiselect";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_customer_group_multiselect");
    },

    getIconClass: function () {
        return "coreshop_icon_customer_groups";
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

        var store = pimcore.globalmanager.get("coreshop_customer_groups");

        var options = {
            name: "restrictTo",
            triggerAction: "all",
            editable: false,
            fieldLabel: t("restrict_selection_to"),
            store: store,
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
        store.load();

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
