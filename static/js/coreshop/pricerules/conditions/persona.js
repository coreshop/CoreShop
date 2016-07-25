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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.conditions.persona');

pimcore.plugin.coreshop.pricerules.conditions.persona = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'persona',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('personas');

        var persona = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_persona'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'text',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'persona',
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.persona)
                        this.setValue(me.data.persona);
                }
            }
        };

        if (this.data && this.data.persona) {
            persona.value = this.data.persona;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                persona
            ]
        });

        return this.form;
    }
});
