<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\DataHub\Resolver;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MultiResourceResolver
{
    public function __construct(
        protected Data $fieldDefinition,
        protected RepositoryInterface $repository,
    ) {
    }

    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $resource = $value[$this->fieldDefinition->getName()];

        if (is_array($resource)) {
            $result = [];

            foreach ($resource as $val) {
                $val = $this->repository->find($val);

                if ($val instanceof ResourceInterface) {
                    $result[] = $val->getId();
                }
            }

            return $result;
        }

        return null;
    }
}
