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
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param MetadataInterface $metadata
     * @param RepositoryInterface $repository
     * @param FactoryInterface $factory
     * @param ObjectManager $manager
     * @param ViewHandler $viewHandler
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        MetadataInterface $metadata,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        ObjectManager $manager,
        ViewHandler $viewHandler,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->manager = $manager;
        $this->viewHandler = $viewHandler;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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
        $data = $this->repository->findAll();

        return $this->viewHandler->handle($data, ["group" => "List"]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAction(Request $request)
    {
        $dataModel = $this->findOr404($request->get("id"));

        return $this->viewHandler->handle(['data' => $dataModel, 'success' => true], ["group" => "Detailed"]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $data = $request->get('data');
        $jsonData = $this->decodeJson($data);
        $dataModel = $this->get('jms_serializer')->deserialize($data, $this->metadata->getClass('model'), 'json');

        if ($this->entityManager->contains($dataModel)) {
            if ($dataModel instanceof TranslatableInterface) {
                if (array_key_exists('_translations', $jsonData)) {
                    foreach ($jsonData['_translations'] as $lang => $values) {
                        $translation = $dataModel->getTranslation($lang);

                        foreach ($values as $key => $val) {
                            $setter = "set" . ucfirst($key);

                            if (method_exists($translation, $setter)) {
                                $translation->$setter($val);
                            }
                        }
                    }
                }
            }

            $this->eventDispatcher->dispatchPreEvent('save', $this->metadata, $dataModel, $request);

            $this->entityManager->flush();

            return $this->viewHandler->handle(['data' => $dataModel, 'success' => true], ["group" => "Detailed"]);
        }

        return $this->viewHandler->handle(['success' => false]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $name = $request->get('name');

        if (strlen($name) <= 0) {
            return $this->viewHandler->handle(['success' => false]);
        } else {
            $dataModel = $this->factory->createNew();

            if ($dataModel instanceof ResourceInterface) {
                $dataModel->setValue('name', $name);
            }

            $this->entityManager->persist($dataModel);
            $this->entityManager->flush();


            return $this->viewHandler->handle(['data' => $dataModel, 'success' => true], ["group" => "Detailed"]);
        }
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $dataModel = $this->repository->find($id);

        if ($dataModel instanceof ResourceInterface) {
            $this->entityManager->remove($dataModel);
            $this->entityManager->flush();

            return $this->viewHandler->handle(['success' => true]);
        }

        return $this->viewHandler->handle(['success' => false]);
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