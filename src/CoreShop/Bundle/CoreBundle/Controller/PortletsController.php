<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Core\Portlet\ExportPortletInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\SerializerInterface;

class PortletsController extends AdminController
{
    public function getPortletDataAction(
        Request $request,
        ViewHandlerInterface $viewHandler,
        ServiceRegistryInterface $portletsRegistry
    ): Response {
        $portletName = $request->get('portlet');

        if (!$portletsRegistry->has($portletName)) {
            throw new \InvalidArgumentException(sprintf('Portlet %s not found', $portletName));
        }

        /** @var PortletInterface $portlet */
        $portlet = $portletsRegistry->get($portletName);

        return $viewHandler->handle([
            'success' => true,
            'data' => $portlet->getPortletData($request->query),
        ]);
    }

    public function exportPortletCsvAction(
        Request $request,
        ServiceRegistryInterface $portletsRegistry,
        SerializerInterface $serializer
    ): Response {
        $portletName = $request->get('portlet');

        if (!$portletsRegistry->has($portletName)) {
            throw new \InvalidArgumentException(sprintf('Portlet %s not found', $portletName));
        }

        /** @var PortletInterface $portlet */
        $portlet = $portletsRegistry->get($portletName);

        if ($portlet instanceof ExportPortletInterface) {
            $data = $portlet->getExportPortletData($request->query);
        } else {
            $data = $portlet->getPortletData($request->query);
        }

        $csvData = $serializer->serialize($data, 'csv');

        $response = new Response($csvData);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.csv', $portletName)
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
