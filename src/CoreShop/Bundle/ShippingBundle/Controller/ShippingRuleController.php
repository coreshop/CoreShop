<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\RuleFormSchemaCollector;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingRuleController extends ResourceController
{
    public function getConfigAction(
        Request $request,
        RuleFormSchemaCollector $schemaCollector,
        #[Autowire(service: 'coreshop.form_registry.shipping_rule.conditions')]
        FormTypeRegistryInterface $conditionFormRegistry,
        #[Autowire(service: 'coreshop.form_registry.shipping_rule.actions')]
        FormTypeRegistryInterface $actionFormRegistry,
    ): Response {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        $conditionSchemas = $schemaCollector->collectSchemasWithTypeMap($conditionFormRegistry, array_keys($conditions));
        $actionSchemas = $schemaCollector->collectSchemasWithTypeMap($actionFormRegistry, array_keys($actions));

        return $this->viewHandler->handle([
            'actions' => array_keys($actions),
            'conditions' => array_keys($conditions),
            'schemas' => array_merge(
                $conditionSchemas['schemas'],
                $actionSchemas['schemas'],
            ),
            'conditionSchemaByType' => $conditionSchemas['schemaByType'],
            'actionSchemaByType' => $actionSchemas['schemaByType'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function getConfigActions(): array
    {
        return $this->getParameter('coreshop.shipping_rule.actions');
    }

    /**
     * @return array<string, string>
     */
    protected function getConfigConditions(): array
    {
        return $this->getParameter('coreshop.shipping_rule.conditions');
    }
}
