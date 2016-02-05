/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.update");
pimcore.plugin.coreshop.update = Class.create({

    checkAvailableUpdates: function () {

        Ext.MessageBox.confirm("CONFIRMATION",
            'You are about to update CoreShop. <br />'
            + 'Please do not update this CoreShop installation unless you are sure what you are doing.<br/>'
            + '<b style="color:red;"><u>Updates should be performed only by developers!</u></b><br />'
            + 'Are you sure?',
            function (buttonValue) {
                if (buttonValue == "yes") {

                    this.window = new Ext.Window({
                        layout: 'fit',
                        width: 500,
                        height: 385,
                        autoScroll: true,
                        modal: true
                    });

                    this.window.show();

                    // start
                    this.checkFilePermissions();
                }
            }.bind(this));

    },

    checkFilePermissions: function () {

        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: "Liveupdate",
            bodyStyle: "padding: 20px;",
            html: "<b>Checking file permissions in /plugins/CoreShop</b><br /><br />"
        }));
        this.window.updateLayout();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/check-file-permissions",
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if (res && res.success) {
                    this.checkForAvailableUpdates();
                } else {
                    this.window.removeAll();
                    this.window.add(new Ext.Panel({
                        title: 'ERROR',
                        bodyStyle: "padding: 20px;",
                        html: '<div class="pimcore_error"><b>Some file in /plugins/CoreShop is not writeable!</b> <br />'
                        + 'Please ensure that the whole /pimcore directory is writeable.</div>'
                    }));
                    this.window.updateLayout();
                }
            }.bind(this)
        });
    },

    checkForAvailableUpdates: function () {
        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: 'Liveupdate',
            bodyStyle: "padding: 20px;",
            html: "Looking for updates ..."
        }));
        this.window.updateLayout();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/get-available-updates",
            success: this.selectUpdate.bind(this)
        });
    },

    selectUpdate: function (response) {

        this.window.removeAll();

        var availableUpdates;

        try {
            availableUpdates = Ext.decode(response.responseText);
        }
        catch (e) {

            this.window.add(new Ext.Panel({
                title: "ERROR",
                bodyStyle: "padding: 20px;",
                autoScroll: true,
                html: '<div class="pimcore_error"><b>Unable to retrieve update information, see the error below:</b>'
                + '</div> <br />' + response.responseText
            }));
            this.window.updateLayout();

            return;
        }

        // github not reachable.
        if (availableUpdates.master === false && availableUpdates.releases === false) {

            var panel = new Ext.Panel({
                html: t('coreshop_server_is_currently_offline'),
                bodyStyle: "padding: 20px;"
            });

            this.window.add(panel);
            this.window.updateLayout();

            return;

        // no updates available
        } else if (availableUpdates.master.length < 1 && availableUpdates.releases.length < 1) {

            var panel = new Ext.Panel({
                html: t('coreshop_latest_version_already_installed'),
                bodyStyle: "padding: 20px;"
            });

            this.window.add(panel);
            this.window.updateLayout();

            return;
        }

        var panelConfig = {
            items: []
        };

        if (availableUpdates.releases.length > 0) {

            var storeReleases = new Ext.data.Store({
                proxy: {
                    type: 'memory',
                    reader: {
                        type: 'json',
                        rootProperty: 'releases',
                        idProperty: 'sha'
                    }
                },
                autoDestroy: true,
                data: availableUpdates,
                fields: ["sha", "date", "message"]
            });

            panelConfig.items.push({
                xtype: "form",
                bodyStyle: "padding: 10px;",
                style: "margin-bottom: 10px;",
                title: t('stable_updates'),
                items: [
                    {
                        xtype: "combo",
                        fieldLabel: t('select_update'),
                        name: "update_releases",
                        id: "update_releases",
                        width: 400,
                        store: storeReleases,
                        triggerAction: "all",
                        displayField: "sha",
                        valueField: "sha"
                    }
                ],
                bbar: [
                    "->",
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_apply",
                        text: t('update'),
                        handler: this.gitUpdateStart.bind(this, "update_releases")
                    }
                ]
            });

        }

        if (availableUpdates.master.length > 0) {

            var storeRevisions = new Ext.data.Store({
                proxy: {
                    type: 'memory',
                    reader: {
                        type: 'json',
                        rootProperty: 'master',
                        idProperty: 'sha'
                    }
                },
                autoDestroy: true,
                data: availableUpdates,
                fields: ["sha", "date", "message"]
            });

            panelConfig.items.push({
                xtype: "form",
                bodyStyle: "padding: 10px;",
                title: t('non_stable_updates'),
                items: [
                    {
                        xtype: "panel",
                        border: false,
                        padding: "0 0 10px 0",
                        html: '<div class="pimcore_error"><b>Warning:</b> The master HEAD ist <b>not tested</b>'
                        + ' and might be <b>corrupted</b>!</div>'
                    },
                    {
                        xtype: "combo",
                        fieldLabel: t('select_update'),
                        name: "update_master",
                        id: "update_master",
                        width: 400,
                        store: storeRevisions,
                        triggerAction: "all",
                        valueField: "sha",
                        displayField: "message"
                    }
                ],
                bbar: [
                    "->",
                    {
                        xtype: "button",
                        text: t('update'),
                        iconCls: "pimcore_icon_apply",
                        handler: function () {

                            Ext.MessageBox.confirm("!!! WARNING !!!", t("sure_to_install_unstable_update"),
                                function (buttonValue) {
                                    if (buttonValue == "yes") {
                                        this.gitUpdateStart("update_master");
                                    }
                                }.bind(this));
                        }.bind(this)
                    }
                ]
            });
        }

        this.window.add(new Ext.Panel(panelConfig));
        this.window.updateLayout();

    },

    gitUpdateStart: function (type) {

        var updateId = Ext.getCmp(type).getValue();
        this.updateId = updateId;


        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: "Liveupdate",
            bodyStyle: "padding: 20px;",
            html: "<b>Installing data, please wait ...<br />"
        }));
        this.window.updateLayout();

        pimcore.helpers.activateMaintenance();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/install-remote-update",
            success: function (response) {

                try {
                    response = Ext.decode(response.responseText);
                    if(!response.success) {
                        throw response;
                    } else {
                        this.finished()
                    }
                } catch (e) {
                    if(typeof response.responseText != "undefined" && !empty(response.responseText)) {
                        response = response.responseText;
                    }
                    this.showErrorMessage("Download fails, see debug.log for more details.<br /><br />"
                    + "Error-Message:<br /><hr />" + this.formatError(response));
                }


            }.bind(this),
            failure: function (response) {

                if(typeof response.responseText != "undefined" && !empty(response.responseText)) {
                    response = response.responseText;
                }
                this.showErrorMessage("Download fails, see debug.log for more details.<br /><hr />"
                + this.formatError(response) );
            }.bind(this),
            params: {toRevision: this.updateId, type : type}
        });

    },


    /**
     * Auto Check on startup
     */
    checkSystem: function () {

        this.window = new Ext.Window({
            layout: 'fit',
            width: 500,
            height: 385,
            autoScroll: true,
            modal: true
        });

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/has-updates",
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if (res && res.hasUpdate) {
                    this.showUpdateNag();
                }
            }.bind(this)
        });

    },

    showUpdateNag: function () {

        Ext.MessageBox.alert(
            "CoreShop needs an update!",
            'Hey, there is some work to do. CoreShop will install some updates.',
            this.updateStart.bind(this)
        );

    },

    updateStart: function (  ) {

        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: "Liveupdate",
            bodyStyle: "padding: 20px;",
            html: "<b>Updating ...</b><br />Please wait!<br />"
        }));

        this.window.show();
        this.window.updateLayout();

        pimcore.helpers.activateMaintenance();

        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/install-update",
            success: this.finished.bind(this)
        });

    },

    finished: function () {

        var message = "<b>Update complete!</b><br />Now it's time to reload pimcore.<br /><br />";

        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: "Update Complete.",
            bodyStyle: "padding: 20px;",
            autoScroll: true,
            html: message
        }));

        this.window.updateLayout();

        pimcore.helpers.deactivateMaintenance();

        window.setTimeout(function () {
            Ext.MessageBox.confirm(t("info"), t("reload_pimcore_changes"), function (buttonValue) {
                if (buttonValue == "yes") {
                    window.location.reload();
                }
            }.bind(this));
        }.bind(this), 1000);
    },

    showErrorMessage: function (message) {
        this.window.removeAll();
        this.window.add(new Ext.Panel({
            title: "ERROR",
            autoHeight: true,
            bodyStyle: "padding: 20px;",
            html: '<div class="pimcore_error">' + message + "</div>"
        }));
        this.window.updateLayout();
    },

    formatError: function (error) {

        if(typeof error.message == "string" || typeof error.message == "number") {
            return error.message;
        } else if (typeof error == "object") {
            return "<pre>"  + htmlentities(FormatJSON(error)) + "</pre>";
        }

        return "No valid error message";

    }
});