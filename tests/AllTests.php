<?php

namespace CoreShop\tests;

use CoreShop\Test\SuiteBase;

/**
 * Class AllTests
 * @package CoreShop\tests
 */
class AllTests extends SuiteBase
{
    /**
     * @return AllTests
     */
    public static function suite()
    {
        $suite = new AllTests('Models');
        $suite->addTest(\CoreShop\Test\Models\AllTests::suite());

        return $suite;
    }
}
