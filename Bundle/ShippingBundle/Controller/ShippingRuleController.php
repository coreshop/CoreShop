<?php

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class ShippingRuleController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        return $this->viewHandler->handle(['actions' => array_keys($actions), 'conditions' => array_keys($conditions)]);
    }

    protected function getConfigActions()
    {
        return $this->getParameter('coreshop.shipping_rule.actions');
    }

    protected function getConfigConditions()
    {
        return $this->getParameter('coreshop.shipping_rule.conditions');
    }
}
