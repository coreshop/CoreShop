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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use FOS\RestBundle\View\ViewHandlerInterface;

class FrontendController extends \Pimcore\Controller\FrontendController
{
    /**
     * @var TemplateConfiguratorInterface
     */
    protected $templateConfigurator;

    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    /**
     * @param TemplateConfiguratorInterface $templateConfigurator
     */
    public function setTemplateConfigurator(TemplateConfiguratorInterface $templateConfigurator)
    {
        $this->templateConfigurator = $templateConfigurator;
    }

    /**
     * @param ViewHandlerInterface $viewHandler
     */
    public function setViewHandler(ViewHandlerInterface $viewHandler)
    {
        $this->viewHandler = $viewHandler;
    }
}
