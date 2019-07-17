<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\DataHub\Resolver;

use CoreShop\Component\Resource\DataHub\DoctrineDeferredBuffer;
use CoreShop\Component\Resource\DataHub\DoctrineProvider;
use CoreShop\Component\Resource\DataHub\GraphHydrator;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class DoctrineToOne
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $doctrineClass;

    /**
     * @var string
     */
    private $graphName;

    /**
     * @var array
     */
    private $association;

    /**
     * @var DoctrineProvider
     */
    private $typeProvider;

    /**
     * @var string The key that is used for storing the buffer.
     */
    private $bufferKey;

    public function __construct(
        DoctrineProvider $provider,
        string $name,
        string $doctrineClass,
        string $graphName,
        array $association
    ) {
        $this->name = $name;
        $this->doctrineClass = $doctrineClass;
        $this->graphName = $graphName;
        $this->association = $association;
        $this->typeProvider = $provider;

        $this->bufferKey = $this->graphName.'.'.$this->name;

    }

    /**
     * Generate the definition for the GraphQL field
     *
     * @return array
     */
    public function getDefinition()
    {
        $outputType = $this->typeProvider->getType($this->graphName);

        $args = array();

        // Create and return the definition array
        return array(
            'name' => $this->name,
            'type' => $outputType,
            'args' => $args,
            'resolve' => function ($parent, $args, $context, $info) {
                $targetIdentifiers = $this->typeProvider->getTypeIdentifiers($this->graphName);
                $doctrineType = $this->typeProvider->getDoctrineType($this->graphName);
                $identifier = [];

                foreach ($targetIdentifiers as $field) {
                    if ($doctrineType->hasAssociation($field)) {

                        $fieldAssociation = $doctrineType->getAssociationMapping($field);

                        $fieldName = $fieldAssociation['joinColumns'][0]['name'];

                        $identifier[$field] = $parent->getDataValue($fieldName);
                        continue;
                    }

                    $targetToSource = $this->association['targetToSourceKeyColumns'];
                    $sourceColumn = $targetToSource[$field];
                    $identifier[$field] = (is_object($parent) ? $parent->getDataValue($sourceColumn) : $parent[$sourceColumn]);
                }

                if ($identifier != null) {

                    // Initialize the buffer, if initialized use the existing one
                    $buffer = $this->typeProvider->initBuffer(DoctrineDeferredBuffer::class, $this->bufferKey);

                    $buffer->add($identifier);

                    // GraphQLPHP will call the deferred resolvers as needed.
                    return new \GraphQL\Deferred(function () use ($buffer, $identifier, $args) {

                        // Populate the buffer with the loaded data
                        $this->loadBuffered($args, $identifier);

                        $em = $this->typeProvider->getManager();

                        $graphHydrator = new GraphHydrator($em);

                        $result = null;

                        $data = $buffer->result(implode(':', array_values($identifier)));

                        if ($data !== null) {
                            $result = $graphHydrator->hydrate($data, $this->doctrineClass);
                        }

                        return $result;
                    });
                }

                return null;
            },
        );
    }

    public function loadBuffered($args, $identifier)
    {
        $buffer = $this->typeProvider->initBuffer(DoctrineDeferredBuffer::class, $this->bufferKey);

        if (!$buffer->isLoaded()) {
            /**
             * @var QueryBuilder $queryBuilder
             */
            $queryBuilder = $this->typeProvider->getRepository($this->doctrineClass)->createQueryBuilder('e');

            if (count(array_keys($identifier)) === 1) {

                $mappedBy = array_keys($identifier)[0];  // Can be any field not just ID.

                $queryBuilder->andWhere($queryBuilder->expr()->in('e.'.$mappedBy, ':'.$mappedBy));
                $queryBuilder->setParameter($mappedBy, $buffer->get());
            } else {
                $cnt = 0;

                $orConditions = [];

                foreach ($buffer->get() as $parentIdentifier) {

                    $recordConditions = [];

                    foreach ($parentIdentifier as $fieldName => $fieldValue) {
                        $recordConditions[] = $queryBuilder->expr()->eq('e.'.$fieldName, ':'.$fieldName.$cnt);
                        $queryBuilder->setParameter($fieldName.$cnt, $fieldValue);
                    }

                    $andX = $queryBuilder->expr()->andX();
                    $andX->addMultiple($recordConditions);

                    $orConditions[] = $andX;

                    $cnt++;
                }

                $orX = $queryBuilder->expr()->orX();
                $orX->addMultiple($orConditions);

                $queryBuilder->andWhere($orX);

            }

            foreach ($args as $name => $values) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('e.'.$name, ':'.$name));
                $queryBuilder->setParameter($name, $values);

            }

            $query = $queryBuilder->getQuery();
            $query->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);

            $results = $query->getResult(Query::HYDRATE_ARRAY);

            $resultsLoaded = array();

            $doctrineType = $this->typeProvider->getDoctrineType($this->graphName);

            foreach ($results as $result) {
                $targetIdentifiers = $this->typeProvider->getTypeIdentifiers($this->graphName);
                $parentValues = [];

                foreach ($targetIdentifiers as $field) {
                    if ($doctrineType->hasAssociation($field)) {
                        $fieldAssociation = $doctrineType->getAssociationMapping($field);
                        $fieldName = $fieldAssociation['joinColumns'][0]['name'];

                        $parentValues[] = $result[$fieldName];
                        continue;
                    }

                    $fieldName = $doctrineType->getColumnName($field);
                    $parentValues[] = $result[$fieldName];
                }

                $resultsLoaded[implode(':', $parentValues)] = $result;
            }

            $buffer->load($resultsLoaded);
        }
    }
}
