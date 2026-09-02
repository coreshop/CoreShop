/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.notification.rule.conditions.invoiceTransition');

coreshop.notification.rule.conditions.invoiceTransition = Class.create(coreshop.notification.rule.conditions.abstractTransition, {
    type: 'invoiceTransition',

    getRepoName: function() {
        return 'coreshop_transitions_invoice';
    }
});
