<?php

declare(strict_types=1);

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

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class PaymentProviderRuleConditionPass extends RegisterRegistryTypePass
{
    public const string PAYMENT_PROVIDER_RULE_CONDITION_TAG = 'coreshop.payment_provider_rule.condition';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.payment_provider_rule.conditions',
            'coreshop.form_registry.payment_provider_rule.conditions',
            'coreshop.payment_provider_rule.conditions',
            self::PAYMENT_PROVIDER_RULE_CONDITION_TAG,
        );
    }
}
