<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\Controller;

use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\StudioGridFilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Controller\UserAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @psalm-suppress InternalClass
 */
class GridController extends UserAwareController
{
    public function getStudioGridFiltersAction(
        string $listType,
        ServiceRegistryInterface $studioGridFilterServiceRegistry,
        TranslatorInterface $translator,
    ): Response {
        $services = [];

        /**
         * @var \Pimcore\Model\User $user
         *
         * @psalm-suppress InternalMethod
         */
        $user = $this->getPimcoreUser();
        /** @var StudioGridFilterInterface $service */
        foreach ($studioGridFilterServiceRegistry->all() as $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $service->getType(),
                'name' => $translator->trans($service->getLabel(), [], 'studio', $user->getLanguage()),
            ];
        }

        return $this->json($services);
    }

    public function getGridActionsAction(
        string $listType,
        ServiceRegistryInterface $gridActionServiceRegistry,
        TranslatorInterface $translator,
    ): Response {
        $services = [];

        /**
         * @var \Pimcore\Model\User $user
         *
         * @psalm-suppress InternalMethod
         */
        $user = $this->getPimcoreUser();
        /** @var GridActionInterface $service */
        foreach ($gridActionServiceRegistry->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $translator->trans($service->getName(), [], 'studio', $user->getLanguage()),
            ];
        }

        return $this->json($services);
    }

    public function applyGridAction(
        Request $request,
        ServiceRegistryInterface $gridActionServiceRegistry,
    ): Response {
        $requestedIds = $request->request->get('ids');
        $actionId = (string) $request->request->get('actionId');

        if (is_string($requestedIds)) {
            $requestedIds = json_decode($requestedIds);
        }

        $success = true;

        if (!$gridActionServiceRegistry->has($actionId)) {
            $success = false;
            $message = sprintf('Action Service %s not found.', $actionId);
        } else {
            try {
                /** @var GridActionInterface $actionService */
                $actionService = $gridActionServiceRegistry->get($actionId);
                $message = $actionService->apply($requestedIds);
            } catch (\Exception $e) {
                $success = false;
                $message = $e->getMessage();
            }
        }

        return $this->json([
            'success' => $success,
            'message' => $message,
        ]);
    }
}
