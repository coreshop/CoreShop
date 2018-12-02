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

pimcore.registerNS('coreshop.filter.condition');

coreshop.filter.condition = Class.create({

    type: null,

    initialize: function (parent, conditions, type, label) {
        this.parent = parent;
        this.conditions = conditions;
        this.type = type;
        this.label = label ? label : type;
    },

    getFieldsStore: function () {
        return this.parent.getFieldsForIndex();
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        // show only defined conditions
        Ext.each(this.conditions, function (condition) {
            addMenu.push({
                iconCls: 'coreshop_filters_icon_conditions_' + condition,
                text: t('coreshop_filters_' + condition),
                handler: _this.addCondition.bind(_this, condition, {})
            });

        });

        this.fieldsContainer = new Ext.Panel({
            iconCls: 'coreshop_filters_' + this.type,
            title: t('coreshop_filters_' + this.label),
            autoScroll: true,
            style: 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.fieldsContainer;
    },

    disable: function () {
        this.fieldsContainer.disable();
    },

    enable: function () {
        this.fieldsContainer.enable();
    },

    addCondition: function (type, data) {
        if (Object.keys(coreshop.filter.conditions).indexOf(type) >= 0) {
            // create condition
            var item = new coreshop.filter.conditions[type](this, data);

            // add logic for brackets
            var tab = this;

            this.fieldsContainer.add(item.getLayout());
            this.fieldsContainer.updateLayout();
        }
    },

    getData: function () {
        // get defined conditions
        var conditionsData = [];
        var conditions = this.fieldsContainer.items.getRange();
        for (var i = 0; i < conditions.length; i++) {
            var conditionItem = conditions[i];
            var conditionClass = conditionItem.xparent;
            var form = conditionClass.form;

            var condition = {};

            if (Ext.isFunction(conditionClass.getData)) {
                condition = conditionClass.getData();
            }
            else {
                condition = form.form.getFieldValues();
            }

            if (conditionClass.data.id) {
                condition['id'] = conditionClass.data.id;
            }

            condition['type'] = conditions[i].xparent.type;

            conditionsData.push(condition);
        }

        return conditionsData;
    }
});
