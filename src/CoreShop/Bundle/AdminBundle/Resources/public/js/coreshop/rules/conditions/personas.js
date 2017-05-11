/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.personas');

pimcore.plugin.coreshop.rules.conditions.personas = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'personas',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('personas');

        var personas = {
            fieldLabel: t('coreshop_condition_personas'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'text',
            valueField: 'id',
            forceSelection: true,
            multiselect : true,
            triggerAction: 'all',
            name:'personas',
            maxHeight : 400,
            delimiter : false,
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.personas)
                        this.setValue(me.data.personas);
                }
            }
        };

        if (this.data && this.data.personas) {
            personas.value = this.data.personas;
        }

        personas = new Ext.ux.form.MultiSelect(personas);

        this.form = new Ext.form.Panel({
            items : [
                personas
            ]
        });

        return this.form;
    }
});
