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

pimcore.registerNS('pimcore.plugin.coreshop.pricerules.conditions.zone');

pimcore.plugin.coreshop.pricerules.conditions.zone = Class.create(pimcore.plugin.coreshop.pricerules.conditions.abstract, {

    type : 'zone',

    getForm : function () {
        var me = this;
        var store = pimcore.globalmanager.get('coreshop_zones');

        var zone = {
            xtype: 'combo',
            fieldLabel: t('coreshop_condition_zone_zone'),
            typeAhead: true,
            listWidth: 100,
            width : 500,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'zone',
            listeners: {
                beforerender: function () {
                    if (!store.isLoaded() && !store.isLoading())
                        store.load();

                    if (me.data && me.data.zone)
                        this.setValue(me.data.zone);
                }
            }
        };

        if (this.data && this.data.zone) {
            zone.value = this.data.zone;
        }

        this.form = new Ext.form.FieldSet({
            items : [
                zone
            ]
        });

        return this.form;
    }
});
