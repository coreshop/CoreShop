<?php

namespace CoreShop\Bundle\ConfigurationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Configuration\Service\ConfigurationServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationController extends ResourceController
{
    public function saveAllAction(Request $request)
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
    private function getConfigurationService() {
        return $this->get('coreshop.configuration.service');
    }
}
