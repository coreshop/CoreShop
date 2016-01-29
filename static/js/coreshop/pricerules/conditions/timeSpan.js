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

pimcore.registerNS("pimcore.plugin.coreshop.pricerules.conditions.timeSpan");

pimcore.plugin.coreshop.pricerules.conditions.timeSpan = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'timeSpan',

    getForm : function() {

        var me = this;

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
            timeFrom.value = Ext.Date.format(tmpDate, "H:i");

            var tmpDate = new Date(intval(this.data.dateTo));
            dateTo.value = tmpDate;
            timeTo.value = Ext.Date.format(tmpDate, "H:i");
        }

        this.dateFromField = new Ext.form.DateField(dateFrom);
        this.timeFromField = new Ext.form.TimeField(timeFrom);

        this.dateToField = new Ext.form.DateField(dateTo);
        this.timeToField = new Ext.form.TimeField(timeTo);

        var dateFromFieldContainer = new Ext.form.FieldContainer({
            xtype:'fieldcontainer',
            fieldLabel: t('coreshop_condition_timeSpan_dateFrom'),
            combineErrors:true,
            layout: 'hbox',
            items:[this.dateFromField, this.timeFromField],
            itemCls:"object_field",
            name : "dateFrom",
            getValue : function() {
                if (me.dateFromField.getValue()) {
                    var date = new Date(me.dateFromField.getValue());
                    var dateString = Ext.Date.format(date, 'Y-m-d');

                    if (me.timeFromField.getValue()) {
                        dateString += " " + Ext.Date.format(new Date(me.timeFromField.getValue()), "H:i");
                    }
                    else {
                        dateString += " 00:00";
                    }

                    return Ext.Date.parseDate(dateString, "Y-m-d H:i").getTime();
                }
            }.bind(this),
            getName : function() {
                return "dateFrom";
            }
        });

        var dateToFieldContainer = new Ext.form.FieldContainer({
            xtype: 'fieldcontainer',
            fieldLabel: t('coreshop_condition_timeSpan_dateTo'),
            combineErrors: true,
            layout: 'hbox',
            items: [this.dateToField, this.timeToField],
            itemCls: "object_field",
            name : "dateTo",
            getValue : function() {
                if (me.dateToField.getValue()) {
                    var date = new Date(me.dateToField.getValue());
                    var dateString = Ext.Date.format(date, 'Y-m-d');

                    if (me.timeToField.getValue()) {
                        dateString += " " + Ext.Date.format(new Date(me.timeToField.getValue()), "H:i");
                    }
                    else {
                        dateString += " 00:00";
                    }

                    return Ext.Date.parseDate(dateString, "Y-m-d H:i").getTime();
                }
            }.bind(this),
            getName : function() {
                return "dateTo";
            }
        });

        this.form = new Ext.form.FieldSet({
            items : [
                dateFromFieldContainer, dateToFieldContainer
            ]
        });

        return this.form;
    }
});