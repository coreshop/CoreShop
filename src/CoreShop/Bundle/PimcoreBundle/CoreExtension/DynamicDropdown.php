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

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement, InvalidArgument, MissingConstructor
 */
class DynamicDropdown extends DataObject\ClassDefinition\Data\ManyToOneRelation
{
    use DynamicDropdownTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopDynamicDropdown';

    public function getDataFromEditmode($data, $object = null, $params = array())
    {
        return Element\Service::getElementById('object', $data);
    }
}
