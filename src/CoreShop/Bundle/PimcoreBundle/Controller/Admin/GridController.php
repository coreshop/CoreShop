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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @psalm-suppress InternalClass
 */
class GridController extends AdminAbstractController
{
    public function getGridFiltersAction(
        string $listType,
        ServiceRegistryInterface $gridFilterServiceRegistry,
        TranslatorInterface $translator,
    ): Response {
        $services = [];
        /**
         * @var \Pimcore\Model\User $user
         *
         * @psalm-suppress InternalMethod
         */
        $user = $this->getAdminUser();
        /** @var GridFilterInterface $service */
        foreach ($gridFilterServiceRegistry->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $translator->trans($service->getName(), [], 'admin', $user->getLanguage()),
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
        $user = $this->getAdminUser();
        /** @var GridActionInterface $service */
        foreach ($gridActionServiceRegistry->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $translator->trans($service->getName(), [], 'admin', $user->getLanguage()),
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
