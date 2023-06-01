<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class PaymentProviderRuleActionPass extends RegisterRegistryTypePass
{
    public const PAYMENT_PROVIDER_RULE_ACTION_TAG = 'coreshop.payment_provider_rule.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.payment_provider_rule.actions',
            'coreshop.form_registry.payment_provider_rule.actions',
            'coreshop.payment_provider_rule.actions',
            self::PAYMENT_PROVIDER_RULE_ACTION_TAG,
        );
    }
}
