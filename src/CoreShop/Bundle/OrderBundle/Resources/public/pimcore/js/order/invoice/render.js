/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
        var frameUrl = Routing.generate('coreshop_admin_order_invoice_render', {id: invoiceId});

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
