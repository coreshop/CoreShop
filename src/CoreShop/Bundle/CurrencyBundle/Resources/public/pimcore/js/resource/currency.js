Ext.define('CoreShop.store.Currency', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.currency',

    name: 'currency',
    fieldLabel: t('coreshop_currency'),
    store: {
        type: 'coreshop_currencies'
    }
});
