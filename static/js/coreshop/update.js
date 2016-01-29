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

pimcore.registerNS("coreshop.update");
coreshop.update = Class.create({

    initialize: function () {

        this.window = new Ext.Window({
            layout:'fit',
            width:500,
            height:385,
            autoScroll: true,
            modal: true
        });



        Ext.Ajax.request({
            url: "/plugin/CoreShop/admin_update/has-updates",
            success: function (response) {
                var res = Ext.decode(response.responseText);
                if(res && res.hasUpdate) {
                    this.showUpdateNag();
                }
            }.bind(this)
        });

    },

    showUpdateNag : function() {

        Ext.MessageBox.alert(
            "CoreShop needs an update!",
            'Hey, there is some work to do. CoreShop will install some updates.',
            this.updateStart.bind(this)
        );
    },

    updateStart: function () {


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
    }

});