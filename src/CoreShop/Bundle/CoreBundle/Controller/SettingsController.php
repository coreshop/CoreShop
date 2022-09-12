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

use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ResourceBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends AdminController
{
    public function getSettingsAction(Request $request): Response
    {
        $settings = [
            'bundle' => [
                'version' => Version::getVersion(),
            ],
            'reports' => array_values($this->container->getParameter('coreshop.reports')),
        ];

        return $this->viewHandler->handle($settings);
    }
}
