<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PimcoreResourceController extends Controller
{
    /**
     * @var PimcoreRepositoryInterface
     */
    protected $repository;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ResourceFormFactoryInterface
     */
    protected $resourceFormFactory;

    /**
     * @param MetadataInterface $metadata
     * @param PimcoreRepositoryInterface $repository
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ResourceFormFactoryInterface $resourceFormFactory
     */
    public function __construct(
        MetadataInterface $metadata,
        PimcoreRepositoryInterface $repository,
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        ResourceFormFactoryInterface $resourceFormFactory
    ) {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->resourceFormFactory = $resourceFormFactory;
    }

    /**
     * @param int $id
     *
     * @return ResourceInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404($id)
    {
        $model = $this->repository->find($id);

        if (null === $model || !$model instanceof ResourceInterface) {
            throw new NotFoundHttpException(sprintf('The "%s" has not been found', $id));
        }

        return $model;
    }
}