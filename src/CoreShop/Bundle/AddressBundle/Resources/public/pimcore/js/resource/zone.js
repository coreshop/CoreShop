Ext.define('CoreShop.store.Zone', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.zone',

    name: 'zone',
    fieldLabel: t('coreshop_zone'),

    initComponent: function () {
        this.store = pimcore.globalmanager.get('coreshop_zones');

        this.callParent();
    }
});