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

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler;

use CoreShop\Component\Customer\Context\CompositeCustomerContext;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeCustomerContextPass extends PrioritizedCompositeServicePass
{
    public const string CUSTOMER_CONTEXT_SERVICE_TAG = 'coreshop.context.customer';

    public function __construct(
        ) {
        parent::__construct(
            CustomerContextInterface::class,
            CompositeCustomerContext::class,
            self::CUSTOMER_CONTEXT_SERVICE_TAG,
            'addContext',
        );
    }
}
