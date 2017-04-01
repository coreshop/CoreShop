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
use CoreShop\Component\Core\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResourceController extends AdminController
{
    /**
     * @var string
     */
    protected $permission;

    /**
     * @var RepositoryInterface
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
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ViewHandler
     */
    protected $viewHandler;

    /**
     * @param MetadataInterface $metadata
     * @param RepositoryInterface $repository
     * @param FactoryInterface $factory
     * @param ObjectManager $manager
     * @param ViewHandler $viewHandler
     */
    public function __construct(
        MetadataInterface $metadata,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        ObjectManager $manager,
        ViewHandler $viewHandler
    ) {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->manager = $manager;
        $this->viewHandler = $viewHandler;
    }

    /**
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403()
    {
        if ($this->getUser()->getPermission($this->permission)) {
            return;
        }

        throw new AccessDeniedException();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $data = $this->repository->getAll();

        return $this->viewHandler->handle($data, ["group" => "List"]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAction(Request $request)
    {
        $model = $this->findOr404($request->get("id"));

        return $this->viewHandler->handle($model, ["group" => "Detailed"]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $model = $this->getById($id);

        if ($data && $model instanceof ResourceInterface) {
            $data = $this->encodeJson($request->get('data'));

            $model->setValues($data);

            $this->prepareSave($model, $data);

            $model->save();

            return $this->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     */
    public function addAction(Request $request)
    {
        $name = $request->get('name');

        if (strlen($name) <= 0) {
            return $this->json(['success' => false, 'message' => $this->get('translator')->trans('Name must be set')]);
        } else {
            $model = $this->create();
            $model->setValues($request->request->all());

            $this->setDefaultValues($model);

            $model->save();

            return $this->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        }
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $model = $this->getById($id);

        if ($model !== null) {
            $model->delete();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
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