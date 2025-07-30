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

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;

class CodeGeneratorLetterResolver
{
    public function findLetters(CartPriceRuleVoucherGeneratorInterface $generator): string
    {
        return match ($generator->getFormat()) {
            CartPriceRuleVoucherCodeGenerator::FORMAT_ALPHABETIC => implode('', range(chr(65), chr(90))),
            CartPriceRuleVoucherCodeGenerator::FORMAT_NUMERIC => implode('', range(chr(48), chr(57))),
            default => implode('', range(chr(65), chr(90))) . implode('', range(chr(48), chr(57))),
        };
    }
}
