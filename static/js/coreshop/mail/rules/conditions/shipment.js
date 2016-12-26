/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.conditions.shipment');

pimcore.plugin.coreshop.mail.rules.conditions.shipment = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'shipment',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_shipment_type'),
                    name: 'shipmentType',
                    value: this.data ? this.data.shipmentType : 3,
                    width: 250,
                    store: [[1, t('coreshop_partial')], [2, t('coreshop_full')], [3, t('coreshop_all')]],
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local'
                }
            ]
        });

        return this.form;
    }
});
