<?php

namespace CoreShop\Test\Models;

use CoreShop\Test\SuiteBase;

class AllTests extends SuiteBase
{
    /**
     * @return AllTests
     */
    public static function suite()
    {
        $suite = new AllTests('Models');

        $tests = [
            '\\CoreShop\\Test\\Models\\Carrier',
            '\\CoreShop\\Test\\Models\\Product',
            '\\CoreShop\\Test\\Models\\Cart',
            '\\CoreShop\\Test\\Models\\CartPriceRule',
            '\\CoreShop\\Test\\Models\\Category',
            '\\CoreShop\\Test\\Models\\Country',
            '\\CoreShop\\Test\\Models\\Currency',
            '\\CoreShop\\Test\\Models\\CustomerGroup',
            '\\CoreShop\\Test\\Models\\Order',
            '\\CoreShop\\Test\\Models\\Taxation',
            '\\CoreShop\\Test\\Models\\Customer',
            '\\CoreShop\\Test\\Models\\Zone',
            '\\CoreShop\\Test\\Models\\Product\\Filter',
            '\\CoreShop\\Test\\Models\\Product\\Index',
            '\\CoreShop\\Test\\Models\\Product\\SpecificPrice',
            '\\CoreShop\\Test\\Models\\Product\\PriceRule',
            '\\CoreShop\\Test\\Models\\Store',
            '\\CoreShop\\Test\\Models\\Configuration'
        ];

        shuffle($tests);
        print("Created the following execution order:\n");

        foreach ($tests as $test) {
            print("    - " . $test . "\n");

            $suite->addTestSuite($test);
        }

        return $suite;
    }
}
