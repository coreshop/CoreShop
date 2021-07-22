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

use CoreShop\Component\Order\Exception\FailedCodeGenerationException;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

class CartPriceRuleVoucherCodeGenerator
{
    const FORMAT_ALPHANUMERIC = 'alphanumeric';
    const FORMAT_ALPHABETIC = 'alphabetic';
    const FORMAT_NUMERIC = 'numeric';

    private FactoryInterface $voucherCodeFactory;
    private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository;
    private CodeGeneratorCheckerInterface $checker;
    private CodeGeneratorLetterResolver $letterResolver;

    public function __construct(
        FactoryInterface $voucherCodeFactory,
        CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
        CodeGeneratorCheckerInterface $checker,
        CodeGeneratorLetterResolver $letterResolver
    )
    {
        $this->voucherCodeFactory = $voucherCodeFactory;
        $this->voucherCodeRepository = $voucherCodeRepository;
        $this->checker = $checker;
        $this->letterResolver = $letterResolver;
    }

    public function generateCodes(CartPriceRuleVoucherGeneratorInterface $generator): array
    {
        $this->assert($generator);

        $generatedVouchers = [];
        $lettersToUse = $this->letterResolver->findLetters($generator);

        for ($i = 0; $i < $generator->getAmount(); $i++) {
            $code = $this->generateCode($lettersToUse, $generator->getLength(), $generator->getPrefix(), $generator->getSuffix(), $generatedVouchers);

            if ($generator->getHyphensOn() > 0) {
                $code = implode('-', str_split($code, $generator->getHyphensOn()));
            }

            /**
             * @var CartPriceRuleVoucherCodeInterface
             */
            $codeObject = $this->voucherCodeFactory->createNew();
            $codeObject->setCode($code);
            $codeObject->setCreationDate(new \DateTime());
            $codeObject->setUsed(false);
            $codeObject->setUses(0);
            $codeObject->setCartPriceRule($generator->getCartPriceRule());

            $generatedVouchers[$code] = $codeObject;
        }

        return $generatedVouchers;
    }

    protected function generateCode(string $letters, int $length, ?string $prefix, ?string $suffix, array $generatedCoupons): string
    {
         Assert::nullOrRange($length, 1, 40, 'Invalid %d code length should be between %d and %d');

        do {
            $code = '';
            $max = strlen($letters);

            if (null !== $prefix) {
                $code = $prefix;
            }

            for ($i=0; $i < $length; $i++) {
                $code .= $letters[random_int(0, $max - 1)];
            }

            if (null !== $suffix) {
                $code .= $suffix;
            }

        } while ($this->isUsedCode($code, $generatedCoupons));

        return $code;
    }

    private function isUsedCode(string $code, array $generatedCoupons): bool
    {
        if (isset($generatedCoupons[$code])) {
            return true;
        }

        return null !== $this->voucherCodeRepository->findOneBy(['code' => $code]);
    }

    private function assert(CartPriceRuleVoucherGeneratorInterface $generator): void
    {
        if (!$this->checker->isGenerationPossible($generator)) {
            throw new FailedCodeGenerationException($generator);
        }
    }
}
