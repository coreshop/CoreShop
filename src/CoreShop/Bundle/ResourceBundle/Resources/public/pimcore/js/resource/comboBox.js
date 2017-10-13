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

    initComponent: function() {
        this.callParent();

        this.setStore(coreshop.deepCloneStore(this.getStore()));
    }
});