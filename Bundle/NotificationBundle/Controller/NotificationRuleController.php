<?php

namespace CoreShop\Bundle\NotificationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class NotificationRuleController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $conditions = [];
        $actions = [];
        $types = [];

        $actionTypes = $this->getParameter('coreshop.notification_rule.actions.types');
        $conditionTypes = $this->getParameter('coreshop.notification_rule.conditions.types');

        foreach ($actionTypes as $type)
        {
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }

        foreach ($conditionTypes as $type)
        {
            if (!in_array($type, $types)) {
                $types[] = $type;
            }
        }

        foreach ($types as $type) {
            $actionParameter = 'coreshop.notification_rule.actions.' . $type;
            $conditionParameter = 'coreshop.notification_rule.conditions.' . $type;

            if ($this->container->hasParameter($actionParameter)) {
                if (!array_key_exists($type, $actions)) {
                    $actions[$type] = [];
                }

                $actions[$type] = array_merge($actions[$type], array_keys($this->getParameter($actionParameter)));
            }

            if ($this->container->hasParameter($conditionParameter)) {
                if (!array_key_exists($type, $conditions)) {
                    $conditions[$type] = [];
                }

                $conditions[$type] = array_merge($conditions[$type], array_keys($this->getParameter($conditionParameter)));
            }
        }

        return $this->viewHandler->handle([
            'success' => true,
            'types' => $types,
            'actions' => $actions,
            'conditions' => $conditions
        ]);
    }
}
