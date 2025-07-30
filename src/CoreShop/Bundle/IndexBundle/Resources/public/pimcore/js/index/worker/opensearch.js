/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.worker.type.opensearch');
coreshop.index.worker.opensearch = Class.create(coreshop.index.worker.abstract, {
  getFields: function (config) {
    var me = this;

    me.clientSelect = me.getClientSelect(config);
    me.indexSettings = me.getIndexSettings(config);

    return {
      xtype: 'panel',
      items: [
        me.clientSelect,
        me.indexSettings,
      ]
    };
  },

  getClientSelect: function (config) {
    const store = new Ext.data.JsonStore({
      data: [],
    });

    Ext.Ajax.request({
      url: Routing.generate('coreshop_index_getOpenSearchClients'),
      method: 'GET',
      success: function (response) {
        const res = Ext.decode(response.responseText);

        store.loadData(res.clients);
      }.bind(this),
    });

    return Ext.create({
      xtype: 'combobox',
      name: 'client',
      fieldLabel: 'Client',
      store: store,
      queryMode: 'local',
      displayField: 'name',
      valueField: 'name',
      length: 255,
      value: config.hasOwnProperty('client') ? config.client : '',
    });
  },

  getIndexSettings: function (config) {
    var me = this;

    me.numberOfShards = Ext.create({
      xtype: 'numberfield',
      name: 'numberOfShards',
      fieldLabel: 'Number of Shards',
      value: config.hasOwnProperty('numberOfShards') ? config.numberOfShards : 1,
      emptyText: 'The number of primary shards in the index. Default is 1.',
    });
    me.numberOfReplicas = Ext.create({
      xtype: 'numberfield',
      name: 'numberOfReplicas',
      fieldLabel: 'Number of Replicas',
      value: config.hasOwnProperty('numberOfReplicas') ? config.numberOfReplicas : 1,
      emptyText: 'The number of replica shards each primary shard should have. Default is 1.',
    });

    return Ext.create({
      xtype: 'fieldset',
      title: 'Index Settings',
      collapsible: true,
      collapsed: true,
      autoHeight: true,
      width: '100%',
      defaultType: 'textfield',
      defaults: {
        width: 700,
        labelWidth: 150,
      },
      items: [
        me.numberOfShards,
        me.numberOfReplicas,
      ]
    });
  },

  getData: function () {
    return {
      client: this.clientSelect.getValue(),
      numberOfShards: this.numberOfShards.getValue(),
      numberOfReplicas: this.numberOfReplicas.getValue(),
    };
  },
});
