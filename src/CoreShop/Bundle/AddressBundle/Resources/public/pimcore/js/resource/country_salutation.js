Ext.define('CoreShop.address.CountrySalutation', {
    extend: 'Ext.container.Container',
    alias: 'widget.coreshop.countrySalutation',

    config: {
        country: {
            name: 'country'
        },
        salutation: {
            name: 'salutation'
        }
    },

    initComponent: function () {
        const containerId = Ext.id();

        this.items = [Ext.mergeIf(this.country, {
            xtype: 'coreshop.country',
            store: {
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('coreshop_country_listActive'),
                    reader: {
                        type: 'json',
                    }
                },
                fields: [
                    {name: 'id'},
                    {name: 'name'}
                ],
                autoLoad: true,
                remoteSort: false,
                remoteFilter: false
            },
            name: this.name_country,
            allowBlank: false,
            listeners: {
                change: function(cmb) {
                    const container = cmb.up('container');
                    const salutationCombo = container.down('#salutation');
                    if (!salutationCombo) return;

                    if (cmb.getValue() === null) {
                        salutationCombo.setValue(null);
                        salutationCombo.setDisabled(true);
                    }
                    else {
                        Ext.Ajax.request({
                            url: Routing.generate('coreshop_country_get'),
                            method: 'get',
                            params: {
                                id: cmb.getValue()
                            },
                            success: function (response) {
                                var res = Ext.decode(response.responseText);
                                if (res.success) {
                                    salutationCombo.setStore(res.data.salutations.map(function(entry) {
                                        return [entry, t('coreshop_salutation_' + entry)];
                                    }));
                                    salutationCombo.setDisabled(false);
                                }
                            }.bind(this)
                        });
                    }
                }
            }
        }), Ext.mergeIf(this.salutation, {
            xtype: 'combo',
            fieldLabel: t('coreshop_country_salutation'),
            itemId: 'salutation',
            name: this.name_salutation,
            disabled: true,
            allowBlank: false,
            queryMode: 'local',
            editable: false,
            store: []
        })];

        this.callParent();
    }
});
