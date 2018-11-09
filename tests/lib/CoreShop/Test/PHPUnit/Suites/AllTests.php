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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Test\Setup;
use CoreShop\Test\SuiteBase;
use PHPUnit\Framework\TestSuite;
use Pimcore\Bootstrap;

class AllTests extends SuiteBase
{
    public static function suite()
    {
        \Pimcore::setKernel(self::createKernel());
        \Pimcore::getKernel()->boot();

        Setup::setupPimcore();
        Setup::setupCoreShop();

        $suite = new TestSuite('Models');

        $tests = [
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Carrier',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Product',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Cart',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\CartPriceRule',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Category',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Country',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Currency',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\CustomerGroup',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Order',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Quote',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\OrderInvoice',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\OrderShipment',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Taxation',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Zone',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Product\\Filter',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Product\\Index',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Product\\SpecificPrice',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Product\\PriceRule',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Store',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\Configuration',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\ShippingRule',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\NotificationRule',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\StorageList',
            '\\CoreShop\\Test\\PHPUnit\\Suites\\BatchListing',
        ];

        shuffle($tests);
        echo "Created the following execution order:\n";

        foreach ($tests as $test) {
            echo '    - '.$test."\n";

            $suite->addTestSuite($test);
        }

        echo "Install Test Data:\n";
        \CoreShop\Test\Data::createData();

        return $suite;
    }
}
