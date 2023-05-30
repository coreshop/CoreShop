<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResourceController extends AdminController
{
    public function __construct(
        protected MetadataInterface $metadata,
        protected RepositoryInterface $repository,
        protected FactoryInterface $factory,
        protected ObjectManager $manager,
        ViewHandler $viewHandler,
        protected EventDispatcherInterface $eventDispatcher,
        protected ResourceFormFactoryInterface $resourceFormFactory,
        protected ErrorSerializer $formErrorSerializer,
    ) {
        parent::__construct($viewHandler);
    }

    /**
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403(): void
    {
        if ($this->metadata->hasParameter('permission')) {
            $permission = sprintf('%s_permission_%s', $this->metadata->getApplicationName(), $this->metadata->getParameter('permission'));

            $user = $this->getUser();

            if (class_exists(\Pimcore\Security\User\User::class) && $user instanceof \Pimcore\Security\User\User) {
                /**
                 * @psalm-suppress UndefinedClass, UndefinedInterfaceMethod
                 */
                $user = $user->getUser();
            } elseif (class_exists(\Pimcore\Bundle\AdminBundle\Security\User\User::class) && $user instanceof \Pimcore\Bundle\AdminBundle\Security\User\User) {
                /**
                 * @psalm-suppress UndefinedClass, UndefinedInterfaceMethod
                 */
                $user = $user->getUser();
            } else {
                throw new \RuntimeException(sprintf('Unknown Pimcore Admin User Class given "%s"', get_class($user)));
            }

            if ($user->isAllowed($permission)) {
                return;
            }

            throw new AccessDeniedException();
        }
    }

    public function listAction(Request $request): JsonResponse
    {
        $data = $this->repository->findAll();

        return $this->viewHandler->handle($data, ['group' => 'List']);
    }

    public function getAction(Request $request): JsonResponse
    {
        $this->isGrantedOr403();

        $resources = $this->findOr404((int) $this->getParameterFromRequest($request, 'id'));

        return $this->viewHandler->handle(['data' => $resources, 'success' => true], ['group' => 'Detailed']);
    }

    public function saveAction(Request $request): JsonResponse
    {
        $this->isGrantedOr403();

        $resource = $this->findOr404($this->getParameterFromRequest($request, 'id'));

        $form = $this->resourceFormFactory->create($this->metadata, $resource);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            /**
             * @var ResourceInterface $resource
             */
            $resource = $form->getData();

            $this->eventDispatcher->dispatchPreEvent('save', $this->metadata, $resource, $request);

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->manager->clear();

            /**
             * @var ResourceInterface $resource
             */
            $resource = $this->repository->find($resource->getId());

            $this->eventDispatcher->dispatchPostEvent('save', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = $this->formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $this->viewHandler->handle(['success' => false, 'message' => implode(\PHP_EOL, $errors)]);
    }

    public function cloneAction(Request $request): JsonResponse
    {
        $this->isGrantedOr403();

        $resource = $this->factory->createNew();
        $form = $this->resourceFormFactory->create($this->metadata, $resource);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            /**
             * @var ResourceInterface $resource
             */
            $resource = $form->getData();

            $this->eventDispatcher->dispatchPreEvent('clone', $this->metadata, $resource, $request);

            if (method_exists($resource, 'getName')) {
                $resource->setValue('name', sprintf('%s%s', $resource->getName(), time()));
            }

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->manager->clear();

            /**
             * @var ResourceInterface $resource
             */
            $resource = $this->repository->find($resource->getId());

            $this->eventDispatcher->dispatchPostEvent('clone', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = $this->formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $this->viewHandler->handle(['success' => false, 'message' => implode(\PHP_EOL, $errors)]);
    }

    public function addAction(Request $request): JsonResponse
    {
        $this->isGrantedOr403();

        $name = $this->getParameterFromRequest($request, 'name');

        if (strlen($name) <= 0) {
            return $this->viewHandler->handle(['success' => false]);
        }

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

    public function deleteAction(Request $request): JsonResponse
    {
        $this->isGrantedOr403();

        $id = $this->getParameterFromRequest($request, 'id');

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

    public function folderConfigurationAction(): JsonResponse
    {
        $this->isGrantedOr403();

        $repo = $this->repository;

        if (!$repo instanceof PimcoreRepositoryInterface) {
            throw new \InvalidArgumentException('Only Supported with Pimcore Repositories');
        }

        $name = null;
        $folderId = null;

        $folderPath = $this->metadata->getParameter('path');

        if (is_array($folderPath)) {
            $folderPath = reset($folderPath);
        }

        $customerClassDefinition = DataObject\ClassDefinition::getById($repo->getClassId());

        $folder = DataObject::getByPath('/' . $folderPath);

        if ($folder instanceof DataObject\Folder) {
            $folderId = $folder->getId();
        }

        if ($customerClassDefinition instanceof DataObject\ClassDefinition) {
            $name = $customerClassDefinition->getName();
        }

        return $this->viewHandler->handle(['success' => true, 'className' => $name, 'folderId' => $folderId]);
    }

    protected function findOr404(int $id): ResourceInterface
    {
        $model = $this->repository->find($id);

        if (null === $model || !$model instanceof ResourceInterface) {
            throw new NotFoundHttpException(sprintf('The "%s" has not been found', $id));
        }

        return $model;
    }
}
