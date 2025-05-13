/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.document.editables.coreshop_tax_rule_group');
pimcore.document.editables.coreshop_tax_rule_group = Class.create(coreshop.document.editable.select, {

    getType: function() {
        return 'coreshop_tax_rule_group';
    }
});
