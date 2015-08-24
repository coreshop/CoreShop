/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.actions");
pimcore.registerNS("pimcore.plugin.coreshop.pricerule.actions.abstract");
pimcore.plugin.coreshop.pricerule.actions.abstract = Class.create({

    /**
     * pimcore.plugin.coreshop.pricerule.item
     */
    parent: {},

    data : {},

    type : 'abstract',

    initialize : function(parent, data) {
        this.parent = parent;
        this.data = data;
    },

    getLayout : function() {
        this.layout = new Ext.Panel({
            title: t(this.type),
            parent : this,
            style: "margin: 10px 0 0 0",
            items : [
                this.getForm()
            ]
        });

        return this.layout;
    }
});
