/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopProductUnitDefinitions');
pimcore.object.classes.data.coreShopProductUnitDefinitions = Class.create(pimcore.object.classes.data.data, {
    type: 'coreShopProductUnitDefinitions',

    allowIn: {
        object: true,
        objectbrick: false,
        fieldcollection: true,
        localizedfield: true,
        classificationstore: false,
        block: true
    },

    initialize: function (treeNode, initData) {
        this.type = 'coreShopProductUnitDefinitions';
        this.treeNode = treeNode;

        this.initData(initData);
    },

    getTypeName: function () {
        return t('coreshop_product_unit_definitions');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_product_units';
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();
        this.specificPanel.add([
            {
                xtype: 'numberfield',
                fieldLabel: t('width'),
                name: 'width',
                value: this.datax.width
            },
            {
                xtype: 'panel',
                bodyStyle: 'padding-top: 3px',
                style: 'margin-bottom: 10px',
                html: '<span class="object_field_setting_warning">' + t('default_value_warning') + '</span>'
            }
        ]);

        return this.layout;
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
