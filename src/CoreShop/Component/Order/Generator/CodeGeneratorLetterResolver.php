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

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;

class CodeGeneratorLetterResolver
{
    public function findLetters(CartPriceRuleVoucherGeneratorInterface $generator)
    {
        switch ($generator->getFormat()) {
            case CartPriceRuleVoucherCodeGenerator::FORMAT_ALPHABETIC:
                $lettersToUse = implode('', range(chr(65), chr(90)));

                break;
            case CartPriceRuleVoucherCodeGenerator::FORMAT_NUMERIC:
                $lettersToUse = implode('', range(chr(48), chr(57)));

                break;

            case CartPriceRuleVoucherCodeGenerator::FORMAT_ALPHANUMERIC:
            default:
                $lettersToUse = implode('', range(chr(65), chr(90))).implode('', range(chr(48), chr(57)));

                break;
        }

        return $lettersToUse;
    }
}
