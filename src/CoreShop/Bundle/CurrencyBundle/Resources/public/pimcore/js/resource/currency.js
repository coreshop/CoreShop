Ext.define('CoreShop.store.Currency', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.currency',

    name: 'currency',
    fieldLabel: t('coreshop_currency'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('coreshop_currencies');

        this.callParent();
    }
});