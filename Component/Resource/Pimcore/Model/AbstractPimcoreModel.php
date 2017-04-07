<?php

namespace CoreShop\Component\Resource\Pimcore\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\Object\Concrete;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractPimcoreModel extends Concrete implements ResourceInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getById($id, $force = false)
    {
        /**
         * @var static
         */
        $model = parent::getById($id, $force);

        if ($model instanceof static) {
            $model->setContainer(\Pimcore::getContainer());
        }

        return $model;
    }
}
