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

namespace CoreShop\Component\Core\Portlet;

use Symfony\Component\HttpFoundation\ParameterBag;

interface ExportPortletInterface
{
    /**
     * Get data for exporting portlet.
     *
     * @param ParameterBag $parameterBag
     *
     * @return array
     */
    public function getExportPortletData(ParameterBag $parameterBag);
}
