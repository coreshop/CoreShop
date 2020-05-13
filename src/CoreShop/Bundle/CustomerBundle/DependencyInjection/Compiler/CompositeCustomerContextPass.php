<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use CoreShop\Component\Customer\Context\CompositeCustomerContext;
use CoreShop\Component\Customer\Context\CustomerContextInterface;

final class CompositeCustomerContextPass extends PrioritizedCompositeServicePass
{
    public const CUSTOMER_CONTEXT_SERVICE_TAG = 'coreshop.context.customer';

    public function __construct()
    {
        parent::__construct(
            CustomerContextInterface::class,
            CompositeCustomerContext::class,
            self::CUSTOMER_CONTEXT_SERVICE_TAG,
            'addContext'
        );
    }
}
