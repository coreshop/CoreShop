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

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GridController extends AdminAbstractController
{
    public function getGridFiltersAction(string $listType): Response
    {
        $gridFilterRepository = $this->container->get('coreshop.registry.grid.filter');
        $trans = $this->container->get('translator');

        $services = [];
        /** @var GridFilterInterface $service */
        foreach ($gridFilterRepository->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $trans->trans($service->getName(), [], 'admin'),
            ];
        }

        return $this->json($services);
    }

    public function getGridActionsAction(string $listType): Response
    {
        $gridActionRepository = $this->container->get('coreshop.registry.grid.action');
        $trans = $this->container->get('translator');

        $services = [];
        /** @var GridActionInterface $service */
        foreach ($gridActionRepository->all() as $id => $service) {
            if ($service->supports($listType) !== true) {
                continue;
            }

            $services[] = [
                'id' => $id,
                'name' => $trans->trans($service->getName(), [], 'admin'),
            ];
        }

        return $this->json($services);
    }

    public function applyGridAction(Request $request): Response
    {
        $requestedIds = $request->request->get('ids');
        $actionId = (string) $request->request->get('actionId');

        if (is_string($requestedIds)) {
            $requestedIds = json_decode($requestedIds);
        }

        $gridActionRepository = $this->container->get('coreshop.registry.grid.action');

        $success = true;

        if (!$gridActionRepository->has($actionId)) {
            $success = false;
            $message = sprintf('Action Service %s not found.', $actionId);
        } else {
            try {
                /** @var GridActionInterface $actionService */
                $actionService = $gridActionRepository->get($actionId);
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
