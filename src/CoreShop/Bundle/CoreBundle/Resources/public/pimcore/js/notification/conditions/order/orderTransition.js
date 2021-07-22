/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.notification.rule.conditions.orderTransition');

coreshop.notification.rule.conditions.orderTransition = Class.create(coreshop.notification.rule.conditions.abstractTransition, {
    type: 'orderTransition',

    getRepoName: function() {
        return 'coreshop_transitions_order';
    }
});
