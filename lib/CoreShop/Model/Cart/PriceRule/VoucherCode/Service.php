<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Cart\PriceRule\VoucherCode;

use CoreShop\Model\Cart\PriceRule\VoucherCode;
use CoreShop\Model\Dao\AbstractDao;
use Pimcore\Date;

/**
 * Class Service
 * @package CoreShop\Model\Cart\PriceRule\VoucherCode
 */
class Service extends AbstractDao
{
    /**
     *
     */
    const FORMAT_ALPHANUMERIC = "alphanumeric";

    /**
     *
     */
    const FORMAT_ALPHABETIC = "alphabetic";

    /**
     *
     */
    const FORMAT_NUMERIC = "numeric";

    /**
     * Generates Voucher Codes
     *
     * @param $priceRule
     * @param $amount
     * @param $length
     * @param $format
     * @param $prefix
     * @param $suffix
     * @param $hyphensOn
     *
     * @return VoucherCode[]
     */
    public static function generateCodes($priceRule, $amount, $length, $format, $hyphensOn = 0, $prefix = "", $suffix = "")
    {
        $lettersToUse = "";
        $generatedVouchers = [];

        switch ($format) {
            case self::FORMAT_ALPHABETIC:
                $lettersToUse = implode("", range(chr(65), chr(90)));
                break;
            case self::FORMAT_NUMERIC:
                $lettersToUse = implode("", range(chr(48), chr(57)));
                break;

            case self::FORMAT_ALPHANUMERIC:
            default:
                $lettersToUse = implode("", range(chr(65), chr(90))) . implode("", range(chr(48), chr(57)));
                break;
        }

        for ($i = 0; $i < $amount; $i++) {
            $code = $prefix . self::generateCode($lettersToUse, $length) . $suffix;

            if ($hyphensOn > 0) {
                $code = implode("-", str_split($code, $hyphensOn));
            }

            $codeObject = VoucherCode::create();
            $codeObject->setCode($code);
            $codeObject->setCreationDate(Date::now());
            $codeObject->setUsed(false);
            $codeObject->setUses(0);
            $codeObject->setPriceRule($priceRule);
            $codeObject->save();

            $generatedVouchers[] = $codeObject;
        }

        return $generatedVouchers;
    }

    /**
     * Generates a code
     *
     * @param $letters
     * @param $length
     * @return string
     */
    protected static function generateCode($letters, $length)
    {
        srand((double)microtime() * 1000000);
        $i = 0;
        $code = '' ;

        while ($i <= $length) {
            $num = rand() % 33;
            $tmp = substr($letters, $num, 1);
            $code = $code . $tmp;
            $i++;
        }

        return $code;
    }
}
