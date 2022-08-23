/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
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
