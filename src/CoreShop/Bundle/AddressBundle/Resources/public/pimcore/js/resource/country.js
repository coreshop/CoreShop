Ext.define('CoreShop.address.Country', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.country',

    name: 'country',
    fieldLabel: t('coreshop_country'),
    store: {
        type: 'coreshop_countries'
    }
});
