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

namespace CoreShop\Component\Pimcore\BCLayer;

use Pimcore\Model\DataObject\Concrete;

if (interface_exists(\Pimcore\Model\DataObject\ClassDefinition\Data\CustomDataCopyInterface::class)) {
    interface CustomDataCopyInterface extends \Pimcore\Model\DataObject\ClassDefinition\Data\CustomDataCopyInterface
    {
    }
} else {
    /**
     * @method mixed createDataCopy(Concrete $object, $data)
     */
    interface CustomDataCopyInterface
    {

    }
}
