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

declare(strict_types=1);

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Webmozart\Assert\Assert;

class CodeGeneratorChecker implements CodeGeneratorCheckerInterface
{
    public function __construct(private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository, private CodeGeneratorLetterResolver $letterResolver, private float $ratio = 0.5)
    {
    }

    public function isGenerationPossible(CartPriceRuleVoucherGeneratorInterface $generator): bool
    {
        $amountToBeCreated = $generator->getAmount();
        $possibleAmount = $this->calculatePossibleGenerationAmount($generator);

        return $possibleAmount >= $amountToBeCreated;
    }

    public function getPossibleGenerationAmount(CartPriceRuleVoucherGeneratorInterface $generator): int
    {
        return $this->calculatePossibleGenerationAmount($generator);
    }

    private function calculatePossibleGenerationAmount(CartPriceRuleVoucherGeneratorInterface $generator): int
    {
        $amountToBeCreated = $generator->getAmount();
        $length = $generator->getLength();

        Assert::allNotNull(
            [$amountToBeCreated, $length],
            'Code length or amount cannot be null.'
        );

        $generatedAmount = $this->voucherCodeRepository->countCodes(
            $length,
            $generator->getPrefix(),
            $generator->getSuffix()
        );

        $letters = $this->letterResolver->findLetters($generator);

        $codeCombination = strlen($letters) ** $length * $this->ratio;
        if ($codeCombination >= \PHP_INT_MAX) {
            return \PHP_INT_MAX - $generatedAmount;
        }

        return (int)$codeCombination - $generatedAmount;
    }
}
