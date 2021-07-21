<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Webmozart\Assert\Assert;

interface CodeGeneratorCheckerInterface
{
    /**
     * @param CartPriceRuleVoucherGeneratorInterface $generator
     * @return bool
     */
    public function isGenerationPossible(CartPriceRuleVoucherGeneratorInterface $generator);

    /**
     * @param CartPriceRuleVoucherGeneratorInterface $generator
     * @return int
     */
    public function getPossibleGenerationAmount(CartPriceRuleVoucherGeneratorInterface $generator);
}
