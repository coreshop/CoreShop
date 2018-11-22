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

use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class SettingsController extends AdminController
{
    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // permission check
        if (!$this->getUser()->isAllowed('coreshop_permission_settings')) {
            throw new \Exception(sprintf('this function requires "%s" permission!', 'coreshop_permission_settings'));
        }
    }

    public function getSettingsAction(Request $request)
    {
        $settings = [
            'bundle' => [
                'version' => Version::getVersion(),
            ],
            'reports' => array_values($this->getParameter('coreshop.reports')),
        ];

        return $this->viewHandler->handle($settings);
    }
}
