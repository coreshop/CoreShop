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


pimcore.registerNS("pimcore.plugin.coreshop.pricerule.conditions.timeSpan");
pimcore.plugin.coreshop.pricerule.conditions.timeSpan = Class.create(pimcore.plugin.coreshop.pricerule.conditions.abstract, {

    type : 'timeSpan',

    getForm : function() {

        var dateFrom = {
            itemCls:"object_field",
            width:160
        };

        var dateTo = {
            itemCls:"object_field",
            width:160
        };

        var timeFrom = {
            format:"H:i",
            emptyText:"",
            width:120
        };

        var timeTo = {
            format:"H:i",
            emptyText:"",
            width:120
        };

        if (this.data) {
            var tmpDate = new Date(intval(this.data.dateFrom));
            dateFrom.value = tmpDate;
            timeFrom.value = tmpDate.format("H:i");

            var tmpDate = new Date(intval(this.data.dateTo));
            dateTo.value = tmpDate;
            timeTo.value = tmpDate.format("H:i");
        }

        this.dateFromField = new Ext.form.DateField(dateFrom);
        this.timeFromField = new Ext.form.TimeField(timeFrom);

        this.dateToField = new Ext.form.DateField(dateTo);
        this.timeToField = new Ext.form.TimeField(timeTo);

        var dateFromField = new Ext.form.FieldContainer({
            xtype:'fieldcontainer',
            fieldLabel: t('coreshop_condition_timeSpan_dateFrom'),
            combineErrors:true,
            layout: 'hbox',
            items:[this.dateFromField, this.timeFromField],
            itemCls:"object_field",
            name : "dateFrom",
            getValue : function() {
                if (this.dateFromField.getValue()) {
                    var dateString = this.dateFromField.getValue().format("Y-m-d");

                    if (this.timeFromField.getValue()) {
                        dateString += " " + this.timeFromField.getValue();
                    }
                    else {
                        dateString += " 00:00";
                    }

                    return Date.parseDate(dateString, "Y-m-d H:i").getTime();
                }
            }.bind(this)
        });

        var dateToField = new Ext.form.FieldContainer({
            xtype: 'fieldcontainer',
            fieldLabel: t('coreshop_condition_timeSpan_dateTo'),
            combineErrors: true,
            layout: 'hbox',
            items: [this.dateToField, this.timeToField],
            itemCls: "object_field",
            name : "dateTo",
            getValue : function() {
                if (this.dateToField.getValue()) {
                    var dateString = this.dateToField.getValue().format("Y-m-d");

                    if (this.timeToField.getValue()) {
                        dateString += " " + this.timeToField.getValue();
                    }
                    else {
                        dateString += " 00:00";
                    }

                    return Date.parseDate(dateString, "Y-m-d H:i").getTime();
                }
            }.bind(this)
        });

        this.form = new Ext.form.FieldSet({
            items : [
                dateFromField, dateToField
            ]
        });

        return this.form;
    }
});