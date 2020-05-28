/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.create.step.products');
coreshop.order.order.create.step.products = Class.create(coreshop.order.order.create.step.products, {
    getPanel: function ($super) {
        var panel = $super();

        panel.on('beforeedit', function (editor, context) {
            var combo = editor.editor.form.findField('unitDefinition');

            if (context.record.get('units') && context.record.get('units').length > 0) {
                combo.enable();
                combo.getStore().removeAll();
                combo.getStore().loadRawData(context.record.get('units'));
                combo.setValue(context.record.get('unitDefinition'));
            } else {
                combo.disable();
            }


        });

        return panel;
    },

    onRowEditingFinished: function($super, editor, context, eOpts) {
        var combo = editor.editor.form.findField('unitDefinition');

        if (context.record.get('units') && context.record.get('units').length > 0) {
            var record = combo.getStore().getById(combo.getValue());

            if (record) {
                context.record.set('unitDefinitionRecord', record.data);
                context.record.set('unitDefinition', record.get('id'));
            }
            else {
                context.record.set('unitDefinitionRecord', null);
                context.record.set('unitDefinition', null);
            }
        }

        $super(editor, context, eOpts);
    },

    generateItemGrid: function ($super) {
        var itemGrid = $super(),
            columns = itemGrid.columns;

        if (!Ext.isArray(columns)) {
            return itemGrid;
        }

        // insert unit definition before "total"
        columns.splice((columns.length - 3), 0, {
            xtype: 'gridcolumn',
            dataIndex: 'unitDefinition',
            text: t('coreshop_unit'),
            width: 100,
            align: 'right',
            renderer: function (value, metaData, record) {
                if (!record.get('unitDefinitionRecord')) {
                    return '--';
                }

                return record.get('unitDefinitionRecord').name;
            },
            field: {
                xtype: 'combo',
                mode: 'local',
                displayField: 'name',
                valueField: 'id',
                forceSelection: true,
                triggerAction: 'all',
                queryMode: 'local',
                allowBlank: true
            }
        });

        itemGrid.columns = columns;

        return itemGrid;
    }
});
