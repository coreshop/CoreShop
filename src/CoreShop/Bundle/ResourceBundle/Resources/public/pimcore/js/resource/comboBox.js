Ext.define('CoreShop.resource.ComboBox', {
    extend: 'Ext.form.field.ComboBox',
    alias: 'widget.coreshop.combo',

    typeAhead: true,
    mode: 'local',
    listWidth: 100,
    displayField: 'name',
    valueField: 'id',
    forceSelection: true,
    triggerAction: 'all',
    queryMode: 'local',

    config: {
        storeId: null
    },

    initComponent: function() {
        this.callParent();

        if (this.getStoreId()) {
            this.setStore(pimcore.globalmanager.get(this.getStoreId()));
        }
        else {
            this.setStore(coreshop.deepCloneStore(this.getStore()));
        }
    }
});