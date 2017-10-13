Ext.define('CoreShop.store.Currency', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.currency',

    name: 'currency',
    fieldLabel: t('coreshop_currency'),
    store: pimcore.globalmanager.get('coreshop_currencies')
});