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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @property ContainerInterface $container
 */
class FrontendController extends AbstractController
{
    protected TemplateConfiguratorInterface $templateConfigurator;

    public function setTemplateConfigurator(TemplateConfiguratorInterface $templateConfigurator)
    {
        $this->templateConfigurator = $templateConfigurator;
    }

    protected function generateCoreShopUrl($object, $route = null, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        return $this->container->get(LinkGeneratorInterface::class)->generate($object, $route, $parameters, $referenceType);
    }
}
