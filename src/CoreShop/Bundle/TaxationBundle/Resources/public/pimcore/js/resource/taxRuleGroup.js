Ext.define('CoreShop.store.TaxRuleGroup', {
    extend: 'CoreShop.resource.ComboBox',
    alias: 'widget.coreshop.taxRuleGroup',

    name: 'taxRule',
    fieldLabel: t('coreshop_tax_rule_group'),
    storeId: 'coreshop_taxrulegroups',
    listeners: {
        beforerender: function () {
            if (!this.getStore().isLoaded() && !this.getStore().isLoading())
                this.getStore().load();
        }
    }
});