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

pimcore.registerNS('pimcore.object.classes.data.coreShopMoneyCurrency');
pimcore.object.classes.data.coreShopMoneyCurrency = Class.create(pimcore.object.classes.data.data, {
    type: "coreShopMoneyCurrency",

    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: true,
        fieldcollection: true,
        localizedfield: true,
        classificationstore: true,
        block: true
    },

    initialize: function (treeNode, initData) {
        this.type = "coreShopMoneyCurrency";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_money_currency");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "coreshop_icon_money";
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();
        this.specificPanel.add([
            {
                xtype: "numberfield",
                fieldLabel: t("width"),
                name: "width",
                value: this.datax.width
            }
        ]);

        if (!this.isInCustomLayoutEditor()) {
            this.specificPanel.add([
                {
                    xtype: "numberfield",
                    fieldLabel: t("min_value"),
                    name: "minValue",
                    value: this.datax.minValue
                }, {
                    xtype: "numberfield",
                    fieldLabel: t("max_value"),
                    name: "maxValue",
                    value: this.datax.maxValue
                }
            ]);
        }

        return this.layout;
    },

    applySpecialData: function (source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax = {};
            }

            Ext.apply(this.datax, {
                width: source.datax.width,
                minValue: source.datax.minValue,
                maxValue: source.datax.maxValue,
            });
        }
    }
});
