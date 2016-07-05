/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.filters.condition');

pimcore.plugin.coreshop.filters.condition = Class.create({

    type : null,

    initialize : function (parent, conditions, type) {
        this.parent = parent;
        this.conditions = conditions;
        this.type = type;
    },

    getFieldsStore : function () {
        return this.parent.getFieldsForIndex();
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined conditions
        Ext.each(this.conditions, function (condition) {

            if (condition == 'abstract')
                return;

            addMenu.push({
                iconCls: 'coreshop_product_filters_icon_conditions_' + condition,
                text: t('coreshop_product_filters_' + condition),
                handler: _this.addCondition.bind(_this, condition, {})
            });

        });

        this.fieldsContainer = new Ext.Panel({
            iconCls: 'coreshop_product_filters_' + this.type,
            title: t('coreshop_product_filters_' + this.type),
            autoScroll: true,
            style : 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.fieldsContainer;
    },

    disable : function () {
        this.fieldsContainer.disable();
    },

    enable : function () {
        this.fieldsContainer.enable();
    },

    addCondition: function (type, data) {
        // create condition
        var item = new pimcore.plugin.coreshop.filters.conditions[type](this, data);

        // add logic for brackets
        var tab = this;

        this.fieldsContainer.add(item.getLayout());
        this.fieldsContainer.updateLayout();
    },

    getData : function () {
        // get defined conditions
        var conditionsData = [];
        var conditions = this.fieldsContainer.items.getRange();
        for (var i = 0; i < conditions.length; i++) {
            var conditionItem = conditions[i];
            var conditionClass = conditionItem.xparent;
            var form = conditionClass.form;

            var condition = form.form.getFieldValues();
            condition['type'] = conditions[i].xparent.type;

            conditionsData.push(condition);
        }

        return conditionsData;
    }
});
