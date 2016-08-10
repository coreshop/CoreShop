/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.categories');

pimcore.plugin.coreshop.rules.conditions.categories = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'categories',

    categories : null,

    getForm : function () {
        this.categories = new pimcore.plugin.coreshop.object.objectMultihref(this.data ? this.data.categories : [], {
            classes: [
                { classes: coreshop.settings.classMapping.category }
            ],
            name: 'categories',
            title: '',
            height: 200,
            width: 500,
            columns: [],

            columnType: null,
            datatype: 'data',
            fieldtype: 'objects'
        });

        this.form = new Ext.form.FieldSet({
            items : [
                this.categories.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues : function () {
        return {
            categories : this.categories.getValue()
        };
    }
});
