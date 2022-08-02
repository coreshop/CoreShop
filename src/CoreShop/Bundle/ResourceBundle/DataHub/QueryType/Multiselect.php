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

namespace CoreShop\Bundle\ResourceBundle\DataHub\QueryType;

use CoreShop\Bundle\ResourceBundle\DataHub\Resolver\MultiResourceResolver;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Base;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Multiselect extends Base
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(Service $graphQlService, RepositoryInterface $repository)
    {
        parent::__construct($graphQlService);

        $this->repository = $repository;
    }

    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => $this->getResolver($attribute, $fieldDefinition, $class),
        ], $container);
    }

    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf(Type::int());
    }

    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $resolver = new MultiResourceResolver($fieldDefinition, $this->repository);

        return [$resolver, 'resolve'];
    }
}
