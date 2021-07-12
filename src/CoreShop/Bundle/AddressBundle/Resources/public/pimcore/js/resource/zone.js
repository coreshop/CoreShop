Ext.define('CoreShop.address.Zone', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.zone',

    name: 'zone',
    fieldLabel: t('coreshop_zone'),
    store: {
        type: 'coreshop_zones'
    }
});
