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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.conditions.externalEvent');

pimcore.plugin.coreshop.mail.rules.conditions.externalEvent = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'externalEvent',

    getForm : function () {

        var me = this,
            events = new Ext.data.Store({
                restful: false,
                autoload: true,
                proxy: {
                    type: 'ajax',
                    url: '/plugin/CoreShop/admin_mail-rule/get-external-events',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
                reader: new Ext.data.JsonReader({}, [
                    { name:'identifier' },
                    { name:'name' }
                ])
            }
        );

        events.load();

        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                {
                    xtype: 'combo',
                    valueField: 'identifier',
                    displayField: 'name',
                    fieldLabel: t('coreshop_condition_externalEvent'),
                    name: 'externalEvent',
                    value: me.data && me.data.externalEvent ? me.data.externalEvent : null,
                    width: 350,
                    store: events,
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local'
                }
            ]
        });

        return this.form;
    }
});
