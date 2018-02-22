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

namespace CoreShop\Test\Models;

use CoreShop\Test\SuiteBase;

class AllTests extends SuiteBase
{
    /**
     * @return AllTests
     */
    public static function suite()
    {
        $suite = new self('Models');

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
            '\\CoreShop\\Test\\Models\\Quote',
            '\\CoreShop\\Test\\Models\\OrderInvoice',
            '\\CoreShop\\Test\\Models\\OrderShipment',
            '\\CoreShop\\Test\\Models\\Taxation',
            '\\CoreShop\\Test\\Models\\Customer',
            '\\CoreShop\\Test\\Models\\Zone',
            '\\CoreShop\\Test\\Models\\Product\\Filter',
            '\\CoreShop\\Test\\Models\\Product\\Index',
            '\\CoreShop\\Test\\Models\\Product\\SpecificPrice',
            '\\CoreShop\\Test\\Models\\Product\\PriceRule',
            '\\CoreShop\\Test\\Models\\Store',
            '\\CoreShop\\Test\\Models\\Configuration',
            '\\CoreShop\\Test\\Models\\ShippingRule',
            '\\CoreShop\\Test\\Models\\NotificationRule',
            '\\CoreShop\\Test\\Models\\StorageList',
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
