/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.create.step.base');
coreshop.order.sale.create.step.base = Class.create(coreshop.order.sale.create.step.base, {
    isValid: function ($super) {
        var values = this.getValues();

        return $super() && values.currency && values.language;
    },

    getBaseItems: function ($super) {
        var items = $super();

        items.push(
            Ext.create({
                xtype: 'combo',
                name: 'store',
                fieldLabel: t('coreshop_store'),
                store: pimcore.globalmanager.get('coreshop_stores'),
                displayField: 'name',
                valueField: 'id',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: pimcore.globalmanager.get('coreshop_stores').getAt(0),
                listeners: {
                    change: function () {
                        this.eventManager.fireEvent('store.changed');
                    }.bind(this)
                }
            })
        );

        return items;
    }
});