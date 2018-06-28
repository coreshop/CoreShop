Ext.define('CoreShop.store.Store', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.store',

    name: 'store',
    fieldLabel: t('coreshop_store'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('coreshop_stores');

        this.callParent();
    }
});