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


pimcore.registerNS("pimcore.object.classes.data.coreShopOrderState");
pimcore.object.classes.data.coreShopOrderState = Class.create(pimcore.object.classes.data.data, {

    type: "coreShopOrderState",
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
        this.type = "coreShopOrderState";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_order_state");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "coreshop_icon_order_state";
    },

    getLayout: function ($super) {

        $super();

        this.specificPanel.removeAll();

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
