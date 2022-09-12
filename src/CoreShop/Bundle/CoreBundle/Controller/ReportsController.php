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
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ReportsController extends AdminController
{
    public function getReportDataAction(Request $request): Response
    {
        $reportId = $this->getParameterFromRequest($request, 'report');
        $reportRegistry = $this->get('coreshop.registry.reports');

        if (!$reportRegistry->has($reportId)) {
            throw new \InvalidArgumentException(sprintf('Report %s not found', $reportId));
        }

        /** @var ReportInterface $report */
        $report = $reportRegistry->get($reportId);

        return $this->viewHandler->handle([
            'success' => true,
            'data' => $report->getReportData($request->query),
            'total' => $report->getTotal(),
        ]);
    }

    public function exportReportCsvAction(Request $request): Response
    {
        $reportType = $this->getParameterFromRequest($request, 'report');
        $reportRegistry = $this->get('coreshop.registry.reports');

        if (!$reportRegistry->has($reportType)) {
            throw new \InvalidArgumentException(sprintf('Report %s not found', $reportType));
        }

        /** @var ReportInterface $report */
        $report = $reportRegistry->get($reportType);

        if ($report instanceof ExportReportInterface) {
            $data = $report->getExportReportData($request->query);
        } else {
            $data = $report->getReportData($request->query);
        }

        $csvData = $this->get('serializer')->encode($data, 'csv');

        $response = new Response($csvData);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.csv', $reportType),
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
