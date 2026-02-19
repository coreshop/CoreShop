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

coreshop.order.order.detail.blocks.detail = Class.create(coreshop.order.order.detail.blocks.detail, {

    generateItemGrid: function ($super) {

        var itemGrid = $super(),
            columns = itemGrid.columns;

        if (!Ext.isArray(columns)) {
            return itemGrid;
        }

        // insert unit definition before "total"
        columns.splice((columns.length - 3), 0, {
            xtype: 'gridcolumn',
            dataIndex: 'unit',
            text: t('coreshop_unit'),
            width: 100,
            align: 'right',
            renderer: function (value) {
                if (!value) {
                    return '--';
                }

                return value;
            }
        });

        itemGrid.columns = columns;

        return itemGrid;

    }
});
