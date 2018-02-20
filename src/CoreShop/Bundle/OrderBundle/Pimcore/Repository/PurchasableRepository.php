<?php

namespace CoreShop\Bundle\OrderBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\PurchasableRepositoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Pimcore\Model\Object;

class PurchasableRepository extends PimcoreRepository implements PurchasableRepositoryInterface
{
    /**
     * @var array
     */
    private $purchasableImplementations = [];

    /**
     * @param MetadataInterface $metadata
     * @param array $purchasableImplementations
     */
    public function __construct(MetadataInterface $metadata, array $purchasableImplementations)
    {
        parent::__construct($metadata);

        foreach ($purchasableImplementations as $implementation) {
            $this->purchasableImplementations[] = '"' . $implementation . '"';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $list = $this->getList();

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $list = Object::getList();
        $list->addConditionParam(sprintf('o_className IN (%s)', implode(',', $this->purchasableImplementations)));

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function forceFind($id, $force = true)
    {
        $instance = Object::getById($id, $force);

        if (!$instance instanceof PurchasableInterface) {
            return null;
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $criteria[] = [
            'variable' => implode(',', $this->purchasableImplementations)
        ];

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        $instance = parent::findOneBy($criteria);

        if (!$instance instanceof PurchasableInterface) {
            return null;
        }

        return $instance;
    }
}
