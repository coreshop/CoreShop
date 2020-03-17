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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontendController extends \Pimcore\Controller\FrontendController
{
    /**
     * @param mixed  $object|null
     * @param string $route|null
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return mixed|string
     */
    protected function generateCoreShopUrl($object, $route = null, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get(LinkGeneratorInterface::class)->generate($object, $route, $parameters, $referenceType);
    }
}
