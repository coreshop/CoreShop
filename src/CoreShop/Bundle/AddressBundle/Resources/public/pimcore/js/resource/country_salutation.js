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
        this.items = [Ext.mergeIf(this.country, {
            xtype: 'coreshop.country',
            store: {
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/countries/list-active',
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
                    if (cmb.getValue() === null) {
                        cmb.up('panel').down('#salutation').setValue(null);
                        cmb.up('panel').down('#salutation').setDisabled(true);
                    }
                    else {
                        Ext.Ajax.request({
                            url: '/admin/coreshop/countries/get',
                            method: 'get',
                            params: {
                                id: cmb.getValue()
                            },
                            success: function (response) {
                                var res = Ext.decode(response.responseText);

                                if (res.success) {
                                    cmb.up('panel').down('#salutation').setStore(res.data.salutations.map(function(entry) {
                                        return [entry, t('coreshop_salutation_' + entry)];
                                    }));
                                    cmb.up('panel').down('#salutation').setDisabled(false);
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
