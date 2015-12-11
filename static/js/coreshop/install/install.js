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

pimcore.registerNS("pimcore.plugin.coreshop.install");
pimcore.plugin.coreshop.install = Class.create({

    initialize: function () {
        Ext.MessageBox.confirm(t("info"), t("coreshop_install_confirm"), function (buttonValue) {
            if (buttonValue == "yes")
            {
                Ext.Ajax.request({
                    url: "/plugin/CoreShop/admin_install/install",
                    method: "post",
                    success: function (response) {
                        var data = Ext.decode(response.responseText);

                        if(data.success) {
                            Ext.MessageBox.prompt(t("info"), t("coreshop_installed_successfully"), function () {
                                window.location.reload();
                            });
                        }
                        else {
                            Ext.MessageBox.alert(t("alert"), t("coreshop_install_failed"));
                        }
                    }
                });
            }
        }.bind(this));
    }
});