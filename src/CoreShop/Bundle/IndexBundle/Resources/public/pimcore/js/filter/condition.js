/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
                handler: _this.addCondition.bind(_this, condition, {}, true)
            });
        });

        const buttons = [];

        if (!this.parent || this.parent.isAllowed('edit')) {
            buttons.push({
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            });
        }

        this.fieldsContainer = new Ext.Panel({
            iconCls: 'coreshop_filters_' + this.type,
            title: t('coreshop_filters_' + this.label),
            autoScroll: true,
            style: 'padding: 10px',
            forceLayout: true,
            tbar: buttons,
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

            const itemLayout = item.getLayout();

            if (this.parent && !this.parent.isAllowed('edit')) {
                itemLayout.disable();
            }

            this.fieldsContainer.add(itemLayout);
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
            condition['sort'] = (i + 1);

            conditionsData.push(condition);
        }

        return conditionsData;
    }
});
