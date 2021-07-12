Ext.define('CoreShop.address.State', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.state',

    name: 'state',
    fieldLabel: t('coreshop_state'),
    store: {
        type: 'coreshop_states'
    }
});
