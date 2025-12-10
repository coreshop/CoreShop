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

pimcore.registerNS('pimcore.object.classes.data.coreShopProductQuantityPriceRules');
pimcore.object.classes.data.coreShopProductQuantityPriceRules = Class.create(coreshop.object.classes.data.data, {

    type: 'coreShopProductQuantityPriceRules',
    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: false,
        fieldcollection: false,
        localizedfield: false
    },

    initialize: function (treeNode, initData) {
        this.initData(initData);

        this.treeNode = treeNode;
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();

        return this.layout;
    },

    getTypeName: function () {
        return t('coreshop_product_quantity_price_rules');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_product_quantity_price_rules';
    }
});
