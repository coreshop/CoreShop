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

pimcore.registerNS('coreshop.currency.item');
coreshop.currency.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_currency',

    routing: {
        save: 'coreshop_currency_save'
    },

    getFormPanelItems: function () {
        return [
            {
                fieldLabel: t('name'),
                name: 'name',
                value: this.data.name
            },
            {
                fieldLabel: t('coreshop_currency_isoCode'),
                name: 'isoCode',
                value: this.data.isoCode
            },
            {
                xtype: 'numberfield',
                fieldLabel: t('coreshop_currency_numericIsoCode'),
                name: 'numericIsoCode',
                value: this.data.numericIsoCode
            },
            {
                fieldLabel: t('coreshop_currency_symbol'),
                name: 'symbol',
                value: this.data.symbol
            }
        ];
    },

    getSaveData: function () {
        return this.formPanel.getForm().getFieldValues();
    }
});
