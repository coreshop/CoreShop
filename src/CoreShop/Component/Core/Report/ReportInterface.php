<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Report;

use Symfony\Component\HttpFoundation\ParameterBag;

interface ReportInterface
{
    /**
     * Get data for report.
     *
     * @param ParameterBag $parameterBag
     *
     * @return array
     */
    public function getReportData(ParameterBag $parameterBag): array;

    /**
     * Get total amount of found records.
     *
     * @return int
     */
    public function getTotal(): int;
}
