pimcore.registerNS('pimcore.object.gridcolumn.operator.storeprice');

pimcore.object.gridcolumn.operator.storeprice = Class.create(pimcore.object.gridcolumn.Abstract, {
    type: 'operator',
    class: 'StorePrice',
    iconCls: 'coreshop_icon_operator_store_price',
    defaultText: 'coreshop_operator_store_price',
    group: 'coreshop',

    getConfigTreeNode: function (configAttributes) {
        var node;

        if (configAttributes) {
            var nodeLabel = this.getNodeLabel(configAttributes);
            node = {
                draggable: true,
                iconCls: this.iconCls,
                text: nodeLabel,
                configAttributes: configAttributes,
                isTarget: true,
                isChildAllowed: this.allowChild,
                expanded: true,
                leaf: false,
                expandable: false
            };
        } else {
            //For building up operator list
            configAttributes = {type: this.type, class: this.class};

            node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
                configAttributes: configAttributes,
                isTarget: true,
                leaf: true,
                isChildAllowed: this.allowChild
            };
        }

        node.isOperator = true;
        return node;
    },

    getCopyNode: function (source) {
        return source.createNode({
            iconCls: this.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: false,
            expandable: false,
            isOperator: true,
            isChildAllowed: this.allowChild,
            configAttributes: {
                label: source.data.text,
                type: this.type,
                class: this.class
            }
        });
    },

    getConfigDialog: function (node) {
        this.node = node;

        this.textField = new Ext.form.TextField({
            fieldLabel: t('label'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        this.storeField = Ext.create({
            xtype: 'coreshop.store',
            value: this.node.data.configAttributes.storeId
        });

        this.configPanel = new Ext.Panel({
            layout: 'form',
            bodyStyle: 'padding: 10px;',
            items: [
                this.textField,
                this.storeField
            ],
            buttons: [{
                text: t('apply'),
                iconCls: 'pimcore_icon_apply',
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 400,
            height: 200,
            modal: true,
            title: t('coreshop_operator_store_price_settings'),
            layout: 'fit',
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function () {
        this.node.data.configAttributes.label = this.textField.getValue();
        this.node.data.configAttributes.storeId = this.storeField.getValue();

        var nodeLabel = this.getNodeLabel(this.node.data.configAttributes);
        this.node.set('text', nodeLabel);
        this.node.set('isOperator', true);

        this.window.close();
    },

    allowChild: function (targetNode, dropNode) {
        return false;
    },

    getNodeLabel: function (configAttributes) {
        return configAttributes.label;
    }
});