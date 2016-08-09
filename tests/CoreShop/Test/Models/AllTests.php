<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Test\SuiteBase;

class AllTests extends SuiteBase
{
    public static function suite()
    {
        $suite = new AllTests('Models');

        $tests = array(
            '\\CoreShop\\Test\\Models\\Carrier',
            '\\CoreShop\\Test\\Models\\Product',
            '\\CoreShop\\Test\\Models\\Cart',
            //'\\CoreShop\\Test\\Models\\Category',
            '\\CoreShop\\Test\\Models\\Country',
            '\\CoreShop\\Test\\Models\\Currency',
            '\\CoreShop\\Test\\Models\\CustomerGroup',
            //'\\CoreShop\\Test\\Models\\Order',
            //'\\CoreShop\\Test\\Models\\OrderState',
            '\\CoreShop\\Test\\Models\\CartPriceRule',
            '\\CoreShop\\Test\\Models\\Tax',
            '\\CoreShop\\Test\\Models\\TaxRule',
            //'\\CoreShop\\Test\\Models\\User',
            '\\CoreShop\\Test\\Models\\Zone',
            //'\\CoreShop\\Test\\Models\\Product\\Filter',
            //'\\CoreShop\\Test\\Models\\Product\\Index',
            '\\CoreShop\\Test\\Models\\Product\\SpecificPrice',
            '\\CoreShop\\Test\\Models\\Product\\PriceRule',
            '\\CoreShop\\Test\\Models\\Shop'
        );

        shuffle($tests);
        print("Created the following execution order:\n");

        foreach ($tests as $test) {
            print("    - " . $test . "\n");

            $suite->addTestSuite($test);
        }

        return $suite;
    }
}
