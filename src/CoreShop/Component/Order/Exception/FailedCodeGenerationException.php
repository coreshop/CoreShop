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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Exception;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;

final class FailedCodeGenerationException extends \InvalidArgumentException
{
    public function __construct(
        CartPriceRuleVoucherGeneratorInterface $instruction,
        int $exceptionCode = 0,
        ?\Exception $previousException = null,
    ) {
        $message = sprintf(
            'Invalid code length or coupons amount. It is not possible to generate %d unique coupons with %d code length',
            $instruction->getAmount(),
            $instruction->getLength(),
        );

        parent::__construct($message, $exceptionCode, $previousException);
    }
}
