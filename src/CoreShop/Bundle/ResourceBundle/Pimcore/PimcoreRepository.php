<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Intl\Exception\NotImplementedException;

class PimcoreRepository implements PimcoreRepositoryInterface
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function __construct(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource)
    {
        throw new NotImplementedException(sprintf('%s:%s not supported', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        throw new NotImplementedException(sprintf('%s:%s not supported', __CLASS__, __METHOD__));
    }

    /**
     * {@inheritdoc}
     */
    public function getClassId()
    {
        $class = $this->metadata->getClass('model');

        return $class::classId();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->metadata->getClass('model');
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $className = $this->metadata->getClass('model');

        //Refactor as soon as Pimcore introduces changes to $className::getList()
        $listClass = $className.'\\Listing';
        $list = \Pimcore::getContainer()->get('pimcore.model.factory')->build($listClass);

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getList()->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->forceFind($id, false);
    }

    /**
     * {@inheritdoc}
     */
    public function forceFind($id, $force = true)
    {
        $className = $this->metadata->getClass('model');

        return $className::getById($id, $force);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $list = $this->getList();

        $criteria = $this->normalizeCriteria($criteria);

        if (is_array($criteria) && count($criteria) > 0) {
            foreach ($criteria as $criterion) {
                $list->addConditionParam($criterion['condition'], array_key_exists('variable', $criterion) ? $criterion['variable'] : null);
            }
        }

        if (is_array($orderBy) && count($orderBy) > 0) {
            $orderBy = $orderBy[0];

            if (null !== $orderBy) {
                $orderBy = $this->normalizeOrderBy($orderBy);

                if ($orderBy['key']) {
                    $list->setOrderKey($orderBy['key']);
                }

                $list->setOrder($orderBy['direction']);
            }
        }

        $list->setLimit($limit);
        $list->setOffset($offset);

        return $list->load();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        $objects = $this->findBy($criteria);

        if (count($objects) > 0) {
            return $objects[0];
        }

        return null;
    }

    /**
     * Normalize critera input
     *
     * Input could be
     *
     * [
     *     "condition" => "o_id=?",
     *     "conditionVariables" => [1]
     * ]
     *
     * OR
     *
     * [
     *     "condition" => [
     *          "o_id" => 1
     *     ]
     * ]
     *
     * @param $criteria
     * @return array
     */
    private function normalizeCriteria($criteria)
    {
        $normalized = [

        ];

        if (is_array($criteria)) {
            foreach ($criteria as $key => $criterion) {
                $normalizedCriterion = [];

                if (is_array($criterion)) {
                    if (array_key_exists('condition', $criterion)) {
                        if (is_string($criterion['condition'])) {
                            $normalizedCriterion['condition'] = $criterion['condition'];

                            if (array_key_exists('variable', $criterion)) {
                                $normalizedCriterion['variable'] = $criterion['variable'];
                            }
                        }
                    } else {
                        $normalizedCriterion['condition'] = $criterion;
                    }
                } else {
                    $normalizedCriterion['condition'] = $key." = ?";
                    $normalizedCriterion['variable'] = [$criterion];
                }

                if (count($normalizedCriterion) > 0) {
                    $normalized[] = $normalizedCriterion;
                }
            }
        }

        return $normalized;
    }

    /**
     * Normalizes Order By
     *
     * [
     *      "key" => "o_id",
     *      "direction" => "ASC"
     * ]
     *
     * OR
     *
     * "o_id ASC"
     *
     * @param $orderBy
     * @return array
     */
    private function normalizeOrderBy($orderBy)
    {
        $normalized = [
            'key' => '',
            'direction' => 'ASC'
        ];

        if (is_array($orderBy)) {
            if (array_key_exists('key', $orderBy)) {
                $normalized['key'] = $orderBy['key'];
            }

            if (array_key_exists('direction', $orderBy)) {
                $normalized['direction'] = $orderBy['direction'];
            }
        } elseif (is_string($orderBy)) {
            $exploded = explode(" ", $orderBy);

            $normalized['key'] = $exploded[0];

            if (count($exploded) > 1) {
                $normalized['direction'] = $exploded[1];
            }
        }

        return $normalized;
    }
}
