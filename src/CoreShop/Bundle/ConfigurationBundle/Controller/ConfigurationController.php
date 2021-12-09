<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ConfigurationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Configuration\Service\ConfigurationServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends ResourceController
{
    public function saveAllAction(Request $request): Response
    {
        $values = $this->getParameterFromRequest($request, 'values');
        $values = array_htmlspecialchars($values);

        foreach ($values as $key => $value) {
            $this->getConfigurationService()->set($key, $value);
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    private function getConfigurationService(): ConfigurationServiceInterface
    {
        return $this->get(ConfigurationServiceInterface::class);
    }
}
