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

coreshop.store.item = Class.create(coreshop.store.item, {

    getFormPanel: function ($super) {
        var panel = $super();

        panel.down('fieldset').add(
            [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_base_country'),
                    typeAhead: true,
                    value: this.data.baseCountry,
                    mode: 'local',
                    listWidth: 100,
                    store: pimcore.globalmanager.get('coreshop_countries_active'),
                    displayField: 'name',
                    valueField: 'id',
                    forceSelection: true,
                    triggerAction: 'all',
                    name: 'baseCountry'
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: t('coreshop_base_use_gross_prices'),
                    value: this.data.useGrossPrice,
                    name: 'useGrossPrice'
                }
            ]
        );

        return this.formPanel;
    }
});
