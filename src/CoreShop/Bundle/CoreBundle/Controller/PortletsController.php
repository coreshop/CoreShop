<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Portlet\ExportPortletInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PortletsController extends AdminController
{
    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getPortletDataAction(Request $request)
    {
        $portletName = $request->get('portlet');
        $portletRegistry = $this->get('coreshop.registry.portlets');

        if (!$portletRegistry->has($portletName)) {
            throw new \InvalidArgumentException(sprintf('Portlet %s not found', $portletName));
        }

        /** @var PortletInterface $portlet */
        $portlet = $portletRegistry->get($portletName);

        return $this->viewHandler->handle([
            'success' => true,
            'data' => $portlet->getPortletData($request->query),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function exportPortletCsvAction(Request $request)
    {
        $portletName = $request->get('portlet');
        $portletRegistry = $this->get('coreshop.registry.portlets');

        if (!$portletRegistry->has($portletName)) {
            throw new \InvalidArgumentException(sprintf('Portlet %s not found', $portletName));
        }

        /** @var PortletInterface $portlet */
        $portlet = $portletRegistry->get($portletName);

        if ($portlet instanceof ExportPortletInterface) {
            $data = $portlet->getExportPortletData($request->query);
        } else {
            $data = $portlet->getPortletData($request->query);
        }

        $csvData = $this->get('serializer')->encode($data, 'csv');

        $response = new Response($csvData);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.csv', $portletName)
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
