/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.cart.pricerules.conditions.timespan');
coreshop.cart.pricerules.conditions.timespan = Class.create(coreshop.rules.conditions.abstract, {
    type: 'timespan',

    getForm: function () {
        var me = this;

        var dateFrom = {
            itemCls: 'object_field',
            width: 160,
            value: new Date()
        };

        var dateTo = {
            itemCls: 'object_field',
            width: 160,
            value: new Date()
        };

        var timeFrom = {
            format: 'H:i',
            emptyText: '',
            width: 120,
            value: Ext.Date.format(new Date(), 'H:i')
        };

        var timeTo = {
            format: 'H:i',
            emptyText: '',
            width: 120,
            value: Ext.Date.format(new Date(), 'H:i')
        };

        if (this.data) {
            if (this.data.dateFrom) {
                var tmpDateFrom = new Date(intval(this.data.dateFrom));
                dateFrom.value = tmpDateFrom;
                timeFrom.value = Ext.Date.format(tmpDateFrom, 'H:i');
            }

            if (this.data.dateTo) {
                var tmpDateTo = new Date(intval(this.data.dateTo));
                dateTo.value = tmpDateTo;
                timeTo.value = Ext.Date.format(tmpDateTo, 'H:i');
            }
        }

        this.dateFromField = new Ext.form.DateField(dateFrom);
        this.timeFromField = new Ext.form.TimeField(timeFrom);

        this.dateToField = new Ext.form.DateField(dateTo);
        this.timeToField = new Ext.form.TimeField(timeTo);

        this.dateFromFieldContainer = new Ext.form.FieldContainer({
            xtype: 'fieldcontainer',
            fieldLabel: t('coreshop_condition_timespan_dateFrom'),
            combineErrors: true,
            layout: 'hbox',
            items: [this.dateFromField, this.timeFromField],
            itemCls: 'object_field',
            name: 'dateFrom',
            getValue: function () {
                if (me.dateFromField.getValue()) {
                    var date = new Date(me.dateFromField.getValue());
                    var dateString = Ext.Date.format(date, 'Y-m-d');

                    if (me.timeFromField.getValue()) {
                        dateString += ' ' + Ext.Date.format(new Date(me.timeFromField.getValue()), 'H:i');
                    } else {
                        dateString += ' 00:00';
                    }

                    return Ext.Date.parseDate(dateString, 'Y-m-d H:i').getTime();
                }
            }.bind(this),
            getName: function () {
                return 'dateFrom';
            }
        });

        this.dateToFieldContainer = new Ext.form.FieldContainer({
            xtype: 'fieldcontainer',
            fieldLabel: t('coreshop_condition_timespan_dateTo'),
            combineErrors: true,
            layout: 'hbox',
            items: [this.dateToField, this.timeToField],
            itemCls: 'object_field',
            name: 'dateTo',
            getValue: function () {
                if (me.dateToField.getValue()) {
                    var date = new Date(me.dateToField.getValue());
                    var dateString = Ext.Date.format(date, 'Y-m-d');

                    if (me.timeToField.getValue()) {
                        dateString += ' ' + Ext.Date.format(new Date(me.timeToField.getValue()), 'H:i');
                    } else {
                        dateString += ' 00:00';
                    }

                    return Ext.Date.parseDate(dateString, 'Y-m-d H:i').getTime();
                }
            }.bind(this),
            getName: function () {
                return 'dateTo';
            }
        });

        this.form = new Ext.form.Panel({
            items: [
                this.dateFromFieldContainer, this.dateToFieldContainer
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            dateTo: this.dateToFieldContainer.getValue(),
            dateFrom: this.dateFromFieldContainer.getValue()
        };
    }
});
