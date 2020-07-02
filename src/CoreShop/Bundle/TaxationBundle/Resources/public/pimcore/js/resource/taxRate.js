Ext.define('CoreShop.taxation.TaxRate', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.taxRate',

    name: 'taxRate',
    fieldLabel: t('coreshop_tax_rate'),
    store: {
        type: 'coreshop_tax_rates'
    }
});
