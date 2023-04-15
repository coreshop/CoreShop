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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use CoreShop\Component\Core\Portlet\ExportPortletInterface;
use CoreShop\Component\Core\Portlet\PortletInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PortletsController extends AdminController
{
    public function getPortletDataAction(Request $request): Response
    {
        $portletName = $this->getParameterFromRequest($request, 'portlet');
        $portletRegistry = $this->container->get('coreshop.registry.portlets');

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

    public function exportPortletCsvAction(Request $request): Response
    {
        $portletName = $this->getParameterFromRequest($request, 'portlet');
        $portletRegistry = $this->container->get('coreshop.registry.portlets');

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

        $csvData = $this->container->get('serializer')->serialize($data, 'csv');

        $response = new Response($csvData);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.csv', $portletName),
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
