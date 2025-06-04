/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopProductUnitDefinition');
pimcore.object.classes.data.coreShopProductUnitDefinition = Class.create(pimcore.object.classes.data.data, {

    type: 'coreShopProductUnitDefinition',

    allowIn: {
        object: true,
        objectbrick: true,
        fieldcollection: true,
        localizedfield: true,
        classificationstore: true,
        block: true
    },

    initialize: function (treeNode, initData) {
        this.initData(initData);
        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t('coreshop_product_unit_definition');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_product_unit';
    },

    getLayout: function ($super) {

        $super();

        this.specificPanel.removeAll();
        var specificItems = this.getSpecificPanelItems(this.datax);
        this.specificPanel.add(specificItems);

        return this.layout;
    },

    getSpecificPanelItems: function (datax, inEncryptedField) {
        return [{
            xtype: "numberfield",
            fieldLabel: t("width"),
            name: "width",
            value: datax.width
        }];

    },

    applySpecialData: function (source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax = {};
            }
            Ext.apply(this.datax, {
                width: source.datax.width
            });
        }
    }
});
