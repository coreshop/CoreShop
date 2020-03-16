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
use CoreShop\Component\Core\Report\ExportReportInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\SerializerInterface;

class ReportsController extends AdminController
{
    public function getReportDataAction(
        Request $request,
        ServiceRegistryInterface $reportsRegistry,
        ViewHandlerInterface $viewHandler
    ): Response {
        $report = $request->get('report');

        if (!$reportsRegistry->has($report)) {
            throw new \InvalidArgumentException(sprintf('Report %s not found', $report));
        }

        /** @var ReportInterface $report */
        $report = $reportsRegistry->get($report);

        return $viewHandler->handle([
            'success' => true,
            'data' => $report->getReportData($request->query),
            'total' => $report->getTotal(),
        ]);
    }

    public function exportReportCsvAction(
        Request $request,
        ServiceRegistryInterface $reportsRegistry,
        SerializerInterface $serializer
    ): Response {
        $reportType = $request->get('report');

        if (!$reportsRegistry->has($reportType)) {
            throw new \InvalidArgumentException(sprintf('Report %s not found', $reportType));
        }

        /** @var ReportInterface $report */
        $report = $reportsRegistry->get($reportType);

        if ($report instanceof ExportReportInterface) {
            $data = $report->getExportReportData($request->query);
        } else {
            $data = $report->getReportData($request->query);
        }

        $csvData = $serializer->encode($data, 'csv');

        $response = new Response($csvData);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.csv', $reportType)
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
