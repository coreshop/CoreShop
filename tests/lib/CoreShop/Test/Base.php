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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test;

use CoreShop\Exception;
use CoreShop\Model\Carrier;
use CoreShop\Model\Configuration;
use Pimcore\Model\Object\ClassDefinition;

class Base extends \PHPUnit_Framework_TestCase
{
    public function printTestName()
    {
        try {
            throw new Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            print("### running ...  " . $trace[1]["class"] . "::" . $trace[1]["function"] . " ... good luck!\n");
        }
    }

    /**
     *
     */
    public function setUp()
    {
        Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);
    }
}
