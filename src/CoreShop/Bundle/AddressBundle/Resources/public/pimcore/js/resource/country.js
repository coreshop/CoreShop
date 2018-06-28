Ext.define('CoreShop.store.Country', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.country',

    name: 'country',
    fieldLabel: t('coreshop_country'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('coreshop_countries');

        this.callParent();
    }
});