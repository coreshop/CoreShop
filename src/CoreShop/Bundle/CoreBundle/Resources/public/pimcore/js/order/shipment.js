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

coreshop.order.order.shipment = Class.create(coreshop.order.order.shipment, {
    order: null,
    cb: null,

    show: function ($super, shipAbleItems) {
        pimcore.globalmanager.get('coreshop_carriers').load();

        var window = $super(shipAbleItems);

        var carrier = Ext.create('Ext.form.ComboBox', {
            xtype: 'combo',
            fieldLabel: t('coreshop_carrier'),
            mode: 'local',
            store: pimcore.globalmanager.get('coreshop_carriers'),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name: 'carrier',
            value: parseInt(this.order.carrier),
            afterLabelTextTpl: [
                '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
            ],
            allowBlank: false,
            required: true
        });

        window.down('form').insert(0, carrier);

        return window;
    }
});
