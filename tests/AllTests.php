<?php

namespace CoreShop\Tests;

use CoreShop\Test\SuiteBase;

class AllTests extends SuiteBase
{

    public static function suite()
    {
        $suite = new AllTests('Models');
        $suite->addTest(\CoreShop\Test\Models\AllTests::suite());

        return $suite;
    }
}
