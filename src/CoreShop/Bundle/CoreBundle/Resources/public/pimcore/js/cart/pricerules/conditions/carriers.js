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

pimcore.registerNS('coreshop.cart.pricerules.conditions.carriers');
coreshop.cart.pricerules.conditions.carriers = Class.create(coreshop.rules.conditions.abstract, {
    type: 'carriers',

    getForm: function () {
        var me = this;

        var carriers = {
            fieldLabel: t('coreshop_carrier'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_carriers'
            },
            displayField: 'identifier',
            valueField: 'id',
            forceSelection: true,
            multiSelect: true,
            triggerAction: 'all',
            name: 'carriers',
            maxHeight: 400,
            delimiter: false,
            value: me.data.carriers
        };

        if (this.data && this.data.carriers) {
            carriers.value = this.data.carriers;
        }

        carriers = new Ext.ux.form.MultiSelect(carriers);

        this.form = new Ext.form.Panel({
            items: [
                carriers
            ]
        });

        return this.form;
    }
});
