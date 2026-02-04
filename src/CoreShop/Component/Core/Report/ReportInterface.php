<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Report;

use Symfony\Component\HttpFoundation\ParameterBag;

interface ReportInterface
{
    /**
     * Get data for report.
     */
    public function getReportData(ParameterBag $parameterBag): array;

    /**
     * Get total amount of found records.
     */
    public function getTotal(): int;
}
