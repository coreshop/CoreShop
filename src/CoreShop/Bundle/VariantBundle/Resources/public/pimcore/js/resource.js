/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */
pimcore.registerNS('coreshop.variant');
pimcore.registerNS('coreshop.variant.resource');
coreshop.variant.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.broker.fireEvent('resource.register', 'coreshop.variant', this);
        coreshop.broker.addListener('pimcore.postOpenObject', this.postOpenObject, this);
    },

    postOpenObject: function (tab) {
        const className = tab.data.general.className;

        if (!coreshop.stack.coreshop.variant_aware.includes(className)) {
            return;
        }

        this._enrichProductObject(tab);

        pimcore.layout.refresh();
    },

    _enrichProductObject: function (tab) {

        if (tab.data.general.type === 'object') {
            if(tab?.data?.data?.allowedAttributeGroups?.length) {
                const variantHandler = () => {
                    const store = Ext.create('Ext.data.TreeStore', {
                        proxy: {
                            type: 'ajax',
                            url: Routing.generate('coreshop_admin_variant_attributes', {id: tab.id}),
                            reader: {
                                type: 'json',
                                rootProperty: 'data'
                            },
                        },
                        sorters: [{
                            property: 'sorting',
                            direction: 'ASC'
                        }]
                    });

                    store.on('load', (store, records, successful, operation, eOpts) => {
                        if(!store) {
                            console.error('no data found');
                        }
                    });

                    const applyButton = Ext.create('Ext.Button', {
                        text: t("apply"),
                        iconCls: "pimcore_icon_accept",
                        disabled: true,
                        handler: function() {
                            const groupedAttributes = (tree.getView().getChecked()).reduce((acc, obj) => {
                                const groupId = obj.data.group_id;
                                if (!acc[groupId]) {
                                    acc[groupId] = [];
                                }
                                acc[groupId].push(obj.data.id);
                                return acc;
                            }, {});

                            Ext.Ajax.request({
                                url: Routing.generate('coreshop_admin_variant_generator'),
                                jsonData: { id: tab.id, attributes: groupedAttributes },
                                method: 'POST',
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);
                                    if (res.success === true) {
                                        Ext.Msg.alert(t('success'), res.message);
                                        window.destroy();
                                    } else {
                                        Ext.Msg.alert(t('error'), res.message);
                                    }
                                }.bind(this)
                            });
                        }
                    })

                    const tree = Ext.create('Ext.tree.Panel', {
                        hideHeaders: true,
                        rootVisible: false,
                        store: store,
                        layout: 'fit',
                        listeners: {
                            checkchange: function () {
                                const rootNode = tree.getRootNode();
                                let allParentsHaveSelection = true;

                                rootNode.eachChild(function (parentNode) {
                                    var hasCheckedLeaf = false;

                                    parentNode.eachChild(function (childNode) {
                                        if (childNode.get('checked')) {
                                            hasCheckedLeaf = true;
                                            return false;
                                        }
                                    });

                                    if (!hasCheckedLeaf) {
                                        allParentsHaveSelection = false;
                                        return false;
                                    }
                                });

                                if (allParentsHaveSelection) {
                                    applyButton.enable();
                                } else {
                                    applyButton.disable();
                                }
                            }
                        }
                    });

                    const panel = new Ext.Panel({
                        layout: 'fit',
                        header: false,
                        bodyStyle: "padding:10px",
                        border: false,
                        buttons: [
                            applyButton,
                            {
                                text: t("close"),
                                iconCls: "pimcore_icon_cancel",
                                handler: function() {
                                    window.destroy();
                                }
                            }
                        ],
                        frame: false,
                        items: [
                            tree
                        ],
                    });

                    const window = new Ext.window.Window({
                        closeAction: 'close',
                        height: 400,
                        width: 600,
                        layout: 'fit',
                        items: [
                            panel
                        ],
                        modal: false,
                        plain: true,
                        title: t('coreshop.variant_generator.generate'),
                    });

                    window.show();
                };

                if (tab.toolbar.child('[iconCls="coreshop_icon_logo"]')) {
                    tab.toolbar.child('[iconCls="coreshop_icon_logo"]').menu.add({
                        text: t('coreshop.variant_generator.generate'),
                        scale: 'medium',
                        iconCls: 'pimcore_icon_variant',
                        handler: variantHandler.bind(this, tab)
                    });
                }
                else {
                    tab.toolbar.insert(tab.toolbar.items.length, '-');

                    tab.toolbar.insert(tab.toolbar.items.length, {
                        text: t('coreshop.variant_generator.generate'),
                        scale: 'medium',
                        iconCls: 'pimcore_icon_variant',
                        handler: variantHandler.bind(this, tab)
                    });
                }
            }
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function () {
    new coreshop.variant.resource();
});
