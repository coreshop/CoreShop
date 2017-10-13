Ext.define('CoreShop.store.Store', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.store',

    name: 'store',
    fieldLabel: t('coreshop_store'),
    store: pimcore.globalmanager.get('coreshop_stores')
});