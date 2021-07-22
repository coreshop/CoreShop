/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail.blocks.info');
coreshop.order.sale.detail.blocks.info = Class.create(coreshop.order.sale.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.saleInfo = Ext.create('Ext.panel.Panel', {
            margin: '0 20 20 0',
            border: true,
            flex: 8,
            iconCls: this.iconCls,
            tools: [
                {
                    type: 'coreshop-open',
                    tooltip: t('open'),
                    handler: function () {
                        pimcore.helpers.openObject(me.sale.o_id);
                    }
                }
            ]
        });
    },

    getPriority: function () {
        return 10;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.saleInfo;
    },

    updateSale: function () {
        var me = this;

        me.saleInfo.setTitle(t('coreshop_' + me.panel.type) + ': ' + this.sale.saleNumber + ' (' + this.sale.o_id + ')');
    }
});
