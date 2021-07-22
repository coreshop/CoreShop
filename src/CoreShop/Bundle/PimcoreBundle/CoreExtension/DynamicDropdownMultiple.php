<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation;

class DynamicDropdownMultiple extends ManyToManyObjectRelation
{
    use DynamicDropdownTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopDynamicDropdownMultiple';

    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        //if not set, return null
        if ($data === null || $data === false) {
            return null;
        }

        $objects = [];
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $id) {
                $o = DataObject::getById($id);
                if ($o) {
                    $objects[] = $o;
                }
            }
        }
        //must return array if data shall be set
        return $objects;
    }
}
