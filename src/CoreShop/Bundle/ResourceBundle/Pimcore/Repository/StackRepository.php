<?php

namespace CoreShop\Bundle\ResourceBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Pimcore\Model\DataObject;

class StackRepository extends PimcoreRepository
{
    /**
     * @var array
     */
    private $stackClasses = [];

    /**
     * @var string
     */
    private $interface;

    /**
     * @param MetadataInterface $metadata
     * @param $interface
     * @param array $stackClasses
     */
    public function __construct(MetadataInterface $metadata, $interface, array $stackClasses)
    {
        parent::__construct($metadata);

        $this->interface = $interface;

        foreach ($stackClasses as $class) {
            $this->stackClasses[] = '"'.$class.'"';
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
        $list = DataObject::getList();
        $list->addConditionParam(sprintf('o_className IN (%s)', implode(',', $this->stackClasses)));

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function forceFind($id, $force = true)
    {
        $instance = DataObject::getById($id, $force);

        if (!in_array($this->interface, class_implements($instance), true)) {
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
            'variable' => implode(',', $this->stackClasses),
        ];

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        $instance = parent::findOneBy($criteria);

        if (!in_array($this->interface, class_implements($instance), true)) {
            return null;
        }

        return $instance;
    }
}
