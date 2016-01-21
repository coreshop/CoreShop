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

pimcore.registerNS("pimcore.plugin.coreshop.object.tags.multiselect");
pimcore.plugin.coreshop.object.tags.multiselect = Class.create(pimcore.object.tags.multiselect, {

    getLayoutEdit: function () {

        // generate store
        var store = [];

        if(pimcore.globalmanager.exists("coreshop_" + this.storeName)) {
            store = pimcore.globalmanager.get("coreshop_" + this.storeName);
        }
        else {
            console.log("coreshop_" + this.storeName + " should be added as valid store");
        }

        var options = {
            name: this.fieldConfig.name,
            triggerAction: "all",
            editable: false,
            fieldLabel: this.fieldConfig.title,
            store: store,
            itemCls: "object_field",
            maxHeight : 400,
            queryMode : 'local'
        };

        if (this.fieldConfig.width) {
            options.width = this.fieldConfig.width;
        }
        if (this.fieldConfig.height) {
            options.height = this.fieldConfig.height;
        }

        if (typeof this.data == "string" || typeof this.data == "number") {
            options.value = this.data;
        }

        this.component = new Ext.ux.form.MultiSelect(options);

        return this.component;
    }

});