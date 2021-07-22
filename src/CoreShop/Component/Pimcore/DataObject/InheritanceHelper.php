<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

class InheritanceHelper
{
    /**
     * This function enables usage of inherited values in Pimcore and resets the state of inheritance automatically
     * after your functions is finished.
     */
    public static function useInheritedValues(\Closure $function, bool $inheritValues = true): mixed
    {
        $backup = DataObject\AbstractObject::getGetInheritedValues();
        DataObject\AbstractObject::setGetInheritedValues($inheritValues);

        $result = $function();

        DataObject\AbstractObject::setGetInheritedValues($backup);

        return $result;
    }
}
