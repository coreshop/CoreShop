/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

pimcore.registerNS("coreshop.core.object.store_preview");
coreshop.core.object.store_preview = Class.create({
    store: null,

    initialize: function(object) {
        var me = this;

        me.store = null;
        me.object = object;
    },

    getLayout: function () {
        var me = this;

        if (me.layout == null) {
            var iframeOnLoad = "pimcore.globalmanager.get('object_" + me.object.data.general.id + "').preview.iFrameLoaded()";
            me.frameId = 'coreshop_store_preview_iframe_' + me.object.id;
            me.layout = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_store_preview'),
                border: false,
                autoScroll: true,
                closable: false,
                iconCls: "pimcore_icon_preview",
                bodyCls: "pimcore_overflow_scrolling",
                html: '<iframe src="about:blank" style="width: 100%;" onload="' + iframeOnLoad
                    + '" frameborder="0" id="' + me.frameId + '"></iframe>',
                tbar: [{
                    xtype: 'coreshop.store',
                    listeners: {
                        change: function(cmb, store) {
                            if (store) {
                                me.store = store;
                                me.refresh();
                            }
                        }
                    }
                }]
            });

            me.layout.on("resize", me.setLayoutFrameDimensions.bind(this));
            me.layout.on("activate", me.refresh.bind(this));
        }

        return me.layout;
    },

    createLoadingMask: function() {
        var me = this;

        if (!me.loadMask) {
            me.loadMask = new Ext.LoadMask(
                {
                    target: me.layout,
                    msg:t("please_wait")
                });

            me.loadMask.enable();
        }
    },

    setLayoutFrameDimensions: function (el, width, height, rWidth, rHeight) {
        var me = this;

        Ext.get(me.frameId).setStyle({
            height: (height-7) + "px"
        });
    },

    iFrameLoaded: function () {
        var me = this;

        if (me.loadMask) {
            me.loadMask.hide();
        }
    },

    loadCurrentPreview: function () {
        var me = this,
            date = new Date();

        var url = Routing.generate('coreshop_admin_purchasable_store_preview', {id: me.object.data.general.id, time: date.getTime(), store: me.store});

        try {
            Ext.get(me.frameId).dom.src = url;
        }
        catch (e) {
            console.log(e);
        }
    },

    refresh: function () {
        var me = this;

        if (!me.store) {
            return;
        }

        me.createLoadingMask();
        me.loadMask.enable();
        me.loadCurrentPreview();
    }
});
