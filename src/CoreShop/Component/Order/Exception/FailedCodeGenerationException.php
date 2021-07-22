<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Exception;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;

final class FailedCodeGenerationException extends \InvalidArgumentException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        CartPriceRuleVoucherGeneratorInterface $instruction,
        int $exceptionCode = 0,
        ?\Exception $previousException = null
    ) {
        $message = sprintf(
            'Invalid code length or coupons amount. It is not possible to generate %d unique coupons with %d code length',
            $instruction->getAmount(),
            $instruction->getLength()
        );

        parent::__construct($message, $exceptionCode, $previousException);
    }
}
