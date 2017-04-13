<?php

namespace Payum\Bundle\PayumBundle\Storage;

use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\StorageInterface;
use Pimcore\Model\Object\Concrete;

class PayumPimcoreStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var PimcoreFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param string $className
     * @param PimcoreFactoryInterface $paymentFactory
     * @param PimcoreRepositoryInterface $paymentRepository
     */
    public function __construct($className, PimcoreFactoryInterface $paymentFactory, PimcoreRepositoryInterface $paymentRepository)
    {
        $this->className = $className;
        $this->paymentFactory = $paymentFactory;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->paymentFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function support($model)
    {
        return $model instanceof $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function update($model)
    {
        if ($model instanceof Concrete) {
            $model->setKey(uniqid());
            $model->setParentId(1);
            $model->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($model)
    {
        if ($model instanceof Concrete) {
            $model->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->paymentRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria)
    {
        throw new LogicException('Method is not supported by the storage.');
    }

    /**
     * {@inheritdoc}
     */
    public function identify($model)
    {
        if ($model->getId() <= 0) {
            throw new LogicException('The model must be persisted before usage of this method');
        }

        return new Identity($model->getId(), $model);
    }
}