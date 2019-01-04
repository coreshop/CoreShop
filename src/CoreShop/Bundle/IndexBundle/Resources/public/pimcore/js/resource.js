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

pimcore.registerNS('coreshop.index.resource');
coreshop.index.resource = Class.create(coreshop.resource, {
    initialize: function () {
        coreshop.global.addStore('coreshop_indexes', 'coreshop/indices');
        coreshop.global.addStore('coreshop_filters', 'coreshop/filters');

        coreshop.broker.fireEvent('resource.register', 'coreshop.index', this);
    },
    
    openResource: function(item) {
        if (item === 'index') {
            this.openIndex();
        } else if(item === 'filter') {
            this.openFilter();
        }
    },
    
    openIndex: function() {
        try {
            pimcore.globalmanager.get('coreshop_indexes_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_indexes_panel', new coreshop.index.panel());
        }
    },
    
    openFilter: function() {
        try {
            pimcore.globalmanager.get('coreshop_filters_panel').activate();
        }
        catch (e) {
            pimcore.globalmanager.add('coreshop_filters_panel', new coreshop.filter.panel());
        }
    }
});

coreshop.broker.addListener('pimcore.ready', function() {
    new coreshop.index.resource();
});