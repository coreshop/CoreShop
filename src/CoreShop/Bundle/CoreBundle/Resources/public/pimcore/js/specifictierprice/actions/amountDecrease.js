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

pimcore.registerNS('coreshop.tier_pricing.specific_tier_price.actions.amount_decrease');
coreshop.tier_pricing.specific_tier_price.actions.amount_decrease = Class.create(coreshop.tier_pricing.specific_tier_price.actions.abstract, {
    type: 'percentage_decrease',

    getGridColumns: function() {
        var currencyBaseStore, rangeCurrencyStore,
            cloneStore = function (store) {
                var records = [];
                store.each(function (r) {
                    records.push(r.copy());
                });

                var store2 = new Ext.data.Store({
                    recordType: store.recordType
                });

                store2.add(records);
                store2.insert(0, {
                    name: t('empty'),
                    id: null
                });

                return store2;
            };

        currencyBaseStore = pimcore.globalmanager.get('coreshop_currencies');

        if (currencyBaseStore.isLoaded()) {
            rangeCurrencyStore = cloneStore(currencyBaseStore);
        } else {
            currencyBaseStore.load(function (store) {
                rangeCurrencyStore = cloneStore(store);
            }.bind(this));
        }

        return [
            {
                text: t('coreshop_tier_amount'),
                flex: 1,
                sortable: false,
                dataIndex: 'amount',
                name: 'tier_amount',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0
                    });
                },
                renderer: function (value, cell, record) {
                    var prefix = '';
                    if (record.get('pricingBehaviour') === 'amount_increase') {
                        prefix = '+';
                    } else if (record.get('pricingBehaviour') === 'amount_discount') {
                        prefix = '-';
                    }

                    cell.tdStyle = value === 0 ? 'color: grey; font-style: italic;' : '';

                    if (value === undefined) {
                        // @todo: find currency (from currency row / selector (?)
                        return coreshop.util.format.currency('', 0);
                    } else {
                        return prefix + coreshop.util.format.currency('', parseFloat(value) * 100);
                    }
                }
            },
            {
                text: t('coreshop_tier_currency'),
                flex: 1,
                sortable: false,
                dataIndex: 'currency',
                name: 'currency',
                getEditor: function () {
                    return new Ext.form.ComboBox({
                        store: rangeCurrencyStore,
                        valueField: 'id',
                        displayField: 'name',
                        queryMode: 'local',
                        allowBlank: true
                    });
                },
                renderer: function (currency, cell) {
                    var store, currencyObject;

                    cell.tdStyle = currency === null ? 'color: grey; font-style: italic;' : '';

                    if (!isNaN(currency)) {
                        store = pimcore.globalmanager.get('coreshop_currencies');
                        currencyObject = store.getById(currency);
                        if (currencyObject) {
                            return currencyObject.get('name');
                        }
                    } else if (Ext.isObject(currency) && currency.hasOwnProperty('name')) {
                        return currency.name;
                    }

                    return t('empty');
                }
            },
        ];
    },

    beforeEdit: function(record, editor, context) {
        if (context.column.name === 'tier_amount') {
            if (['amount_decrease', 'amount_increase'].indexOf(record.get('pricingBehaviour')) === -1) {
                return false;
            }
        }
        else if (context.column.name === 'currency') {
            if (['amount_decrease', 'amount_increase'].indexOf(record.get('pricingBehaviour')) === -1) {
                return false;
            }
        }
    },

    storeChange: function(model, field) {
        if (field.getValue() !== 'amount_decrease' && field.getValue() !== 'amount_increase') {
            model.set('amount', 0);
            model.set('currency', null);
        }
    },

    defaultValues: function(lastEntry) {
        return {
            amount: 0,
            currency: lastEntry !== null ? lastEntry.get('currency') : null,
        };
    },

    adjustRangeStoreData: function(entry) {
        if (entry.hasOwnProperty('amount')) {
            var p = parseInt(entry['amount']);

            if (p > 0) {
                entry['amount'] = parseInt(entry['amount']) / 100;
            }
        }

        return entry;
    },

    getData: function(record) {
        var currencyId = null,
            currencyRecord = record.get('currency');
        if (!isNaN(currencyRecord)) {
            currencyId = currencyRecord;
        } else if (Ext.isObject(currencyRecord) && currencyRecord.hasOwnProperty('id')) {
            currencyId = currencyRecord.id;
        }

        return {
            'amount': record.get('amount'),
            'currency': currencyId,
        };
    },
});
