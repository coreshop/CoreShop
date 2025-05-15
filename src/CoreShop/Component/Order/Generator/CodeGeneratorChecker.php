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

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use Webmozart\Assert\Assert;

class CodeGeneratorChecker implements CodeGeneratorCheckerInterface
{
    public function __construct(
        private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        private CodeGeneratorLetterResolver $letterResolver,
        private float $ratio = 0.5,
    ) {
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
            'Code length or amount cannot be null.',
        );

        $generatedAmount = $this->voucherCodeRepository->countCodes(
            $length,
            $generator->getPrefix(),
            $generator->getSuffix(),
        );

        $letters = $this->letterResolver->findLetters($generator);

        $codeCombination = strlen($letters) ** $length * $this->ratio;
        if ($codeCombination >= \PHP_INT_MAX) {
            return \PHP_INT_MAX - $generatedAmount;
        }

        return (int) $codeCombination - $generatedAmount;
    }
}
