/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS("coreshop.document.tag.select");
coreshop.document.tag.select = Class.create(pimcore.document.tag, {

    initialize: function(id, name, options, data, inherited) {
        this.id = id;
        this.name = name;

        this.setupWrapper();
        options = this.parseOptions(options);

        options.listeners = {};

        // onchange event
        if (options.onchange) {
            options.listeners.select = eval(options.onchange);
        }

        if (options["reload"]) {
            options.listeners.select = this.reloadDocument;
        }

        options.name = id + "_editable";
        options.triggerAction = 'all';
        options.editable = false;
        options.value = data;
        options.valueField = 'id';
        options.displayField = 'name';

        this.element = new Ext.form.ComboBox(options);
        this.element.render(id);
    },

    getValue: function () {
        return this.element.getValue();
    },

    getType: function () {
        return "select";
    }
});