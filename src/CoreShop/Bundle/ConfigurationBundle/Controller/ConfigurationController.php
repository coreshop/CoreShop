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

namespace CoreShop\Bundle\ConfigurationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Configuration\Service\ConfigurationServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends ResourceController
{
    public function saveAllAction(Request $request): Response
    {
        $values = $request->get('values');
        $values = array_htmlspecialchars($values);

        foreach ($values as $key => $value) {
            $this->getConfigurationService()->set($key, $value);
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @return ConfigurationServiceInterface
     */
    private function getConfigurationService(): ConfigurationServiceInterface
    {
        return $this->get('coreshop.configuration.service');
    }
}
