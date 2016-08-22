/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.product.specificprice.object');
pimcore.registerNS('pimcore.plugin.coreshop.product.specificprice.object.item');
pimcore.plugin.coreshop.product.specificprice.object.item = Class.create(pimcore.plugin.coreshop.product.specificprice.item, {

    getPanel: function () {
        this.panel = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls : this.iconCls,
            items: this.getItems()
        });

        return this.panel;
    },

    initPanel: function () {
        this.panel = this.getPanel();

        this.parentPanel.getTabPanel().add(this.panel);
    }
});
