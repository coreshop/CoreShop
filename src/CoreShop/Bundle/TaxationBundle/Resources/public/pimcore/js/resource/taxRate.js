Ext.define('CoreShop.store.TaxRate', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.taxRate',

    name: 'taxRate',
    fieldLabel: t('coreshop_tax_rate'),
    store: pimcore.globalmanager.get('coreshop_tax_rates')
});