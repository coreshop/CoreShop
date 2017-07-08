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

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;

class ReportsController extends AdminController
{
    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getReportDataAction(Request $request)
    {
        $report = $request->get('report');
        $reportRegistry = $this->get('coreshop.registry.reports');

        if (!$reportRegistry->has($report)) {
            throw new \InvalidArgumentException(sprintf('Report %s not found', $report));
        }

        $reportData = $reportRegistry->get($report)->getData($request->query);

        return $this->json([
            'success' => true,
            'data' => $reportData
        ]);
    }
}
