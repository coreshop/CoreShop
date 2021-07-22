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

namespace CoreShop\Bundle\ResourceBundle\DataHub\Resolver;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MultiResourceResolver
{
    /**
     * @var Data
     */
    protected $fieldDefinition;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param Data                $fieldDefinition
     * @param RepositoryInterface $repository
     */
    public function __construct(Data $fieldDefinition, RepositoryInterface $repository)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->repository = $repository;
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
