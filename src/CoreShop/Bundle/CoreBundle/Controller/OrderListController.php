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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\OrderList\OrderListBulkInterface;
use CoreShop\Component\Core\OrderList\OrderListFilterInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderListController extends AdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOrderListFiltersAction()
    {
        $orderListFilterRepository = $this->get('coreshop.registry.order_list_filter');

        $services = [];
        /** @var OrderListFilterInterface $service */
        foreach ($orderListFilterRepository->all() as $id => $service) {
            $services[] = [
                'id'   => $id,
                'name' => $service->getName()
            ];
        }

        return $this->json($services);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOrderListBulkAction()
    {
        $orderListFilterRepository = $this->get('coreshop.registry.order_list_bulk');

        $services = [];
        /** @var OrderListBulkInterface $service */
        foreach ($orderListFilterRepository->all() as $id => $service) {
            $services[] = [
                'id'   => $id,
                'name' => $service->getName()
            ];
        }

        return $this->json($services);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function applyOrderListBulkAction(Request $request)
    {
        $requestedIds = $request->request->get('ids');
        $bulkId = $request->request->get('bulkId');

        if (is_string($requestedIds)) {
            $requestedIds = json_decode($requestedIds);
        }

        $orderListBulkRepository = $this->get('coreshop.registry.order_list_bulk');

        $success = true;
        $message = '';

        if (!$orderListBulkRepository->has($bulkId)) {
            $success = false;
            $message = sprintf('Bulk Service %s not found.', $bulkId);
        } else {
            try {
                /** @var OrderListBulkInterface $bulkService */
                $bulkService = $orderListBulkRepository->get($bulkId);
                $message = $bulkService->apply($requestedIds);
            } catch (\Exception $e) {
                $success = false;
                $message = $e->getMessage();
            }
        }

        return $this->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
