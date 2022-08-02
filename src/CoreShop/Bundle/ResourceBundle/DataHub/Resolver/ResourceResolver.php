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

namespace CoreShop\Bundle\ResourceBundle\DataHub\Resolver;

use CoreShop\Component\Resource\Model\ResourceInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ResourceResolver
{
    public function __construct(public Data $fieldDefinition)
    {
    }

    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $resource = $value[$this->fieldDefinition->getName()];

        if ($resource instanceof ResourceInterface) {
            return $resource->getId();
        }

        return null;
    }
}
