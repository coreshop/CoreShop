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

coreshop.provider.item = Class.create(coreshop.provider.item, {
    getFormPanel: function ($super) {
        var panel = $super(),
            data = this.data;


        var logoId = data.logo;
        var logoSelect = new coreshop.object.elementHref({
            id: logoId,
            type: 'asset',
        }, {
            documentsAllowed: false,
            objectsAllowed: false,
            assetsAllowed: true,
            name: 'logo',
            title: 'Logo'
        });

        panel.down("fieldset").add([
            {
                xtype: 'coreshop.store',
                name: 'stores',
                multiSelect: true,
                typeAhead: false,
                value: data.stores
            },
            logoSelect.getLayoutEdit()
        ]);

        this.formPanel = panel;

        return this.formPanel;
    }
});
