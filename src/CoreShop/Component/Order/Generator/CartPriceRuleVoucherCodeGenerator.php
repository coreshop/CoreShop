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

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Exception\FailedCodeGenerationException;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherGeneratorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGenerator;
use Webmozart\Assert\Assert;

class CartPriceRuleVoucherCodeGenerator
{
    const FORMAT_ALPHANUMERIC = 'alphanumeric';

    const FORMAT_ALPHABETIC = 'alphabetic';

    const FORMAT_NUMERIC = 'numeric';

    /**
     * @var FactoryInterface
     */
    private $voucherCodeFactory;

    /**
     * @var CartPriceRuleVoucherRepositoryInterface
     */
    private $voucherCodeRepository;

    /**
     * @var CodeGeneratorCheckerInterface
     */
    private $checker;

    /**
     * @var CodeGeneratorLetterResolver
     */
    private $letterResolver;

    /**
     * @param FactoryInterface                        $voucherCodeFactory
     * @param CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository
     * @param CodeGeneratorCheckerInterface           $checker
     * @param CodeGeneratorLetterResolver             $letterResolver
     */
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

    /**
     * Generates Voucher Codes.
     *
     * @param CartPriceRuleVoucherGeneratorInterface $generator
     *
     * @return CartPriceRuleVoucherCodeInterface[]
     */
    public function generateCodes(CartPriceRuleVoucherGeneratorInterface $generator)
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

    /**
     * @param string      $letters
     * @param int         $length
     * @param string|null $prefix
     * @param string|null $suffix
     * @param array       $generatedCoupons
     * @return string
     * @throws \Exception
     */
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

    /**
     * @throws FailedCodeGenerationException
     */
    private function assert(CartPriceRuleVoucherGeneratorInterface $generator): void
    {
        if (!$this->checker->isGenerationPossible($generator)) {
            throw new FailedCodeGenerationException($generator);
        }
    }
}
