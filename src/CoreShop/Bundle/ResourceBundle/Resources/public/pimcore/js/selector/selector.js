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

pimcore.registerNS('coreshop.selector.selector');
coreshop.selector.selector = Class.create({

    initialize: function (multiselect, callback, classes, config) {
        this.classes = classes;
        this.callback = callback;
        this.multiselect = multiselect;
        this.config = Ext.isDefined(config) ? config : {};

        if (!this.multiselect) {
            this.multiselect = false;
        }

        if (!this.classes) {
            this.classes = [];
            pimcore.globalmanager.get("object_types_store").each(function (rec) {
                this.classes.push(rec.data.text);
            }.bind(this));
        }

        if (!this.callback) {
            this.callback = Ext.emptyFn;
        }

        this.panel = new Ext.Panel({
            border: false,
            layout: "fit"
        });

        var windowWidth = 1000;
        if (this.multiselect) {
            windowWidth = 1250;
        }

        var windowConfig = {
            width: windowWidth,
            height: 550,
            title: t('search'),
            modal: true,
            layout: "fit",
            items: [this.panel]
        };

        this.window = new Ext.Window(windowConfig);

        this.window.show();

        this.current = new coreshop.selector.object(this);
    },

    setSearch: function (panel) {
        delete this.current;
        this.panel.removeAll();
        this.panel.add(panel);

        this.panel.updateLayout();
    },

    commitData: function (data) {
        this.callback(data);
        this.window.close();
    }
});
