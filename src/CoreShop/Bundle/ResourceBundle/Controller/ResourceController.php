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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ResourceFormFactoryInterface
     */
    protected $resourceFormFactory;

    /**
     * @var ErrorSerializer
     */
    protected $formErrorSerializer;

    /**
     * @param MetadataInterface $metadata
     * @param RepositoryInterface $repository
     * @param FactoryInterface $factory
     * @param ObjectManager $manager
     * @param ViewHandler $viewHandler
     * @param EventDispatcherInterface $eventDispatcher
     * @param ResourceFormFactoryInterface $resourceFormFactory
     * @param ErrorSerializer $formErrorSerializer
     */
    public function __construct(
        MetadataInterface $metadata,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        ObjectManager $manager,
        ViewHandler $viewHandler,
        EventDispatcherInterface $eventDispatcher,
        ResourceFormFactoryInterface $resourceFormFactory,
        ErrorSerializer $formErrorSerializer
    )
    {
        $this->metadata = $metadata;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->manager = $manager;
        $this->viewHandler = $viewHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->resourceFormFactory = $resourceFormFactory;
        $this->formErrorSerializer = $formErrorSerializer;
    }

    /**
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403()
    {
        if ($this->metadata->hasParameter('permission')) {
            $permission = sprintf('%s_permission_%s', $this->metadata->getApplicationName(), $this->metadata->getParameter('permission'));
            $user = method_exists($this, 'getAdminUser') ? $this->getAdminUser() : $this->getUser();

            if ($user->isAllowed($permission)) {
                return;
            }

            throw new AccessDeniedException();
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $data = $this->repository->findAll();

        return $this->viewHandler->handle($data, ['group' => 'List']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAction(Request $request)
    {
        $this->isGrantedOr403();

        $resources = $this->findOr404($request->get('id'));

        return $this->viewHandler->handle(['data' => $resources, 'success' => true], ['group' => 'Detailed']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $this->isGrantedOr403();

        $resource = $this->findOr404($request->get('id'));

        $form = $this->resourceFormFactory->create($this->metadata, $resource);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $this->eventDispatcher->dispatchPreEvent('save', $this->metadata, $resource, $request);

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->eventDispatcher->dispatchPostEvent('save', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = $this->formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $this->viewHandler->handle(['success' => false, 'message' => implode(PHP_EOL, $errors)]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $this->isGrantedOr403();

        $name = $request->get('name');

        if (strlen($name) <= 0) {
            return $this->viewHandler->handle(['success' => false]);
        } else {
            $resource = $this->factory->createNew();

            if ($resource instanceof ResourceInterface) {
                $resource->setValue('name', $name);
            }

            foreach ($request->request->all() as $key => $value) {
                $resource->setValue($key, $value);
            }

            $this->eventDispatcher->dispatchPreEvent('create', $this->metadata, $resource, $request);

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->eventDispatcher->dispatchPostEvent('create', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $this->isGrantedOr403();

        $id = $request->get('id');

        $resource = $this->repository->find($id);

        if ($resource instanceof ResourceInterface) {
            $this->eventDispatcher->dispatchPreEvent('delete', $this->metadata, $resource, $request);

            $this->manager->remove($resource);
            $this->manager->flush();

            $this->eventDispatcher->dispatchPostEvent('delete', $this->metadata, $resource, $request);

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
