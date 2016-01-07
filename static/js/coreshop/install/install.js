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