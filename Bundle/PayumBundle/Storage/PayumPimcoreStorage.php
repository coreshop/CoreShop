<?php

namespace CoreShop\Bundle\PayumBundle\Storage;

use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\AbstractStorage;

class PayumPimcoreStorage extends AbstractStorage
{
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
        parent::__construct($className);

        $this->paymentFactory = $paymentFactory;
        $this->paymentRepository = $paymentRepository;
    }

    protected function doUpdateModel($model)
    {
        $model->save();
    }

    protected function doDeleteModel($model)
    {
        $model->delete();
    }

    protected function doGetIdentity($model)
    {
        if (!$model->getId()) {
            throw new LogicException('The model must be persisted before usage of this method');
        }

        return new Identity($model->getId(), $model);
    }

    protected function doFind($id)
    {
        return $this->paymentRepository->find($id);
    }

    public function findBy(array $criteria)
    {
        throw new LogicException('Method is not supported by the storage.');
    }
}