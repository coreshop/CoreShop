<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Order\Generator;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

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
     * @param FactoryInterface $voucherCodeFactory
     */
    public function __construct(FactoryInterface $voucherCodeFactory)
    {
        $this->voucherCodeFactory = $voucherCodeFactory;
    }

    /**
     * Generates Voucher Codes.
     *
     * @param CartPriceRuleInterface $priceRule
     * @param $amount
     * @param $length
     * @param $format
     * @param $prefix
     * @param $suffix
     * @param $hyphensOn
     *
     * @return CartPriceRuleVoucherCodeInterface[]
     */
    public function generateCodes(CartPriceRuleInterface $priceRule, $amount, $length, $format, $hyphensOn = 0, $prefix = '', $suffix = '')
    {
        $generatedVouchers = [];

        switch ($format) {
            case self::FORMAT_ALPHABETIC:
                $lettersToUse = implode('', range(chr(65), chr(90)));
                break;
            case self::FORMAT_NUMERIC:
                $lettersToUse = implode('', range(chr(48), chr(57)));
                break;

            case self::FORMAT_ALPHANUMERIC:
            default:
                $lettersToUse = implode('', range(chr(65), chr(90))).implode('', range(chr(48), chr(57)));
                break;
        }

        for ($i = 0; $i < $amount; ++$i) {
            $code = $prefix.self::generateCode($lettersToUse, $length).$suffix;

            if ($hyphensOn > 0) {
                $code = implode('-', str_split($code, $hyphensOn));
            }

            /**
             * @var CartPriceRuleVoucherCodeInterface
             */
            $codeObject = $this->voucherCodeFactory->createNew();
            $codeObject->setCode($code);
            $codeObject->setCreationDate(new \DateTime());
            $codeObject->setUsed(false);
            $codeObject->setUses(0);
            $codeObject->setCartPriceRule($priceRule);

            $generatedVouchers[] = $codeObject;
        }

        return $generatedVouchers;
    }

    /**
     * Generates a code.
     *
     * @param $letters
     * @param $length
     *
     * @return string
     */
    protected static function generateCode($letters, $length)
    {
        srand((float) microtime() * 1000000);
        $i = 0;
        $code = '';

        while ($i <= $length) {
            $num = rand() % 33;
            $tmp = substr($letters, $num, 1);
            $code = $code.$tmp;
            ++$i;
        }

        return $code;
    }
}
