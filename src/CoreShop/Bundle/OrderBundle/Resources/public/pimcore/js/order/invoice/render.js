/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.invoice.render');
coreshop.invoice.render = Class.create({
    initialize: function (element) {
        this.panels = [];
        this.element = element;
    },

    getLayout: function () {
        if (!this.layout) {
            // create new panel
            this.layout = new Ext.Panel({
                title: t('coreshop_order_pdf'),
                iconCls: 'coreshop_icon_orders_invoice_pdf',
                border: false,
                layout: 'border',
                items: []
            });
        }

        return this.layout;
    },

    reload: function () {
        this.layout.add(this.loadDocument(this.element.id));
    },

    loadDocument: function (invoiceId) {
        var frameUrl = '/admin/coreshop/order-invoice/render?id=' + invoiceId;

        //check for native/plugin PDF viewer
        if (this.hasNativePDFViewer()) {
            frameUrl += '&native-viewer=true';
        }

        var editPanel = new Ext.Panel({
            bodyCls: 'pimcore_overflow_scrolling',
            html: '<iframe src="' + frameUrl + '" frameborder="0" id="coreshop_invoice_preview_' + invoiceId + '"></iframe>',
            region: 'center'
        });
        editPanel.on('resize', function (el, width, height, rWidth, rHeight) {
            Ext.get('coreshop_invoice_preview_' + invoiceId).setStyle({
                width: width + 'px',
                height: (height) + 'px'
            });
        }.bind(this));

        return editPanel;
    },

    hasNativePDFViewer: function () {

        var getActiveXObject = function (name) {
            try {
                return new ActiveXObject(name);
            } catch (e) {
            }
        };

        var getNavigatorPlugin = function (name) {
            for (key in navigator.plugins) {
                var plugin = navigator.plugins[key];
                if (plugin.name == name) return plugin;
            }
        };

        var getPDFPlugin = function () {
            return this.plugin = this.plugin || (function () {
                    if (typeof window['ActiveXObject'] != 'undefined') {
                        return getActiveXObject('AcroPDF.PDF') || getActiveXObject('PDF.PdfCtrl');
                    } else {
                        return getNavigatorPlugin('Adobe Acrobat') || getNavigatorPlugin('Chrome PDF Viewer') || getNavigatorPlugin('WebKit built-in PDF');
                    }
                })();
        };

        return !!getPDFPlugin();
    }
});
