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

pimcore.registerNS('coreshop.object.classes.data.dataMultiselect');
coreshop.object.classes.data.dataMultiselect = Class.create(pimcore.object.classes.data.multiselect, {

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
        this.initData(initData);

        this.treeNode = treeNode;
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();
        const specificItems = this.getSpecificPanelItems(this.datax);
        this.specificPanel.add(specificItems);

        return this.layout;
    },

    getSpecificPanelItems: function (datax) {
        if (typeof datax.options != "object") {
            datax.options = [];
        }

        const stylingItems = [
            {
                xtype: "textfield",
                fieldLabel: t("width"),
                name: "width",
                value: datax.width,
            },
            {
                xtype: "displayfield",
                hideLabel: true,
                value: t('width_explanation'),
            },
            {
                xtype: "textfield",
                fieldLabel: t("height"),
                name: "height",
                value: datax.height,
            },
            {
                xtype: "displayfield",
                hideLabel: true,
                value: t('height_explanation'),
            },
        ];

        if (this.isInCustomLayoutEditor()) {
            return stylingItems;
        }

        return stylingItems.concat([
            {
                xtype: "numberfield",
                fieldLabel: t("maximum_items"),
                name: "maxItems",
                value: datax.maxItems,
                minValue: 0,
            },
            {
                xtype: "combo",
                fieldLabel: t("multiselect_render_type"),
                name: "renderType",
                itemId: "renderType",
                mode: 'local',
                store: [
                    ['list', 'List'],
                    ['tags', 'Tags'],
                ],
                value: datax["renderType"] ? datax["renderType"] : 'list',
                triggerAction: "all",
                editable: false,
                forceSelection: true,
            },
        ]);
    },
});
