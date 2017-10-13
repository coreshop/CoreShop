Ext.define('CoreShop.store.Zone', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.zone',

    name: 'zone',
    fieldLabel: t('coreshop_zone'),
    store: pimcore.globalmanager.get('coreshop_zones')
});