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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\StudioBackend\DataAdapter;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\CoreExtension\ProductQuantityPriceRules;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\StudioFormBundle\Form\Schema\RuleFormSchemaCollector;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\ArrayTransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\DataNormalizerInterface;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\Model\FieldContextData;
use Pimcore\Bundle\StudioBackendBundle\DataObject\Data\SetterDataInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\UserInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final readonly class ProductQuantityPriceRulesAdapter implements SetterDataInterface, DataNormalizerInterface
{
    public function __construct(
        private ArrayTransformerInterface $serializer,
        private ParameterBagInterface $parameterBag,
        private RuleFormSchemaCollector $schemaCollector,
        #[Autowire(service: 'coreshop.form_registry.product_quantity_price_rules.conditions')]
        private FormTypeRegistryInterface $conditionFormRegistry,
    ) {
    }

    public function normalize(
        mixed $value,
        Data $fieldDefinition,
    ): ?array {
        $calculationBehaviourTypes = [];
        $pricingBehaviourTypes = [];

        /** @var array<string> $calculators */
        $calculators = $this->parameterBag->get('coreshop.product_quantity_price_rules.calculators');

        /** @var array<string> $actions */
        $actions = $this->parameterBag->get('coreshop.product_quantity_price_rules.actions');

        foreach ($calculators as $type) {
            $calculationBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_calculator_' . strtolower($type)];
        }

        foreach ($actions as $type) {
            $pricingBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_behaviour_' . strtolower($type)];
        }

        $result = [
            'conditions' => $this->getConfigConditions(),
            'actions' => $this->getConfigActions(),
            'conditionSchemaByType' => $this->getConditionSchemaByType(),
            'actionSchemaByType' => [],
            'rules' => [],
            'stores' => [
                'calculationBehaviourTypes' => $calculationBehaviourTypes,
                'pricingBehaviourTypes' => $pricingBehaviourTypes,
            ],
        ];

        if (!is_array($value)) {
            return $result;
        }

        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->setGroups(['Default', 'Detailed']);

        $serializedRules = [];
        foreach ($value as $rule) {
            if ($rule instanceof ProductQuantityPriceRuleInterface) {
                $serializedRules[] = $this->serializer->toArray($rule, $context);
            }
        }

        $result['rules'] = $serializedRules;

        return $result;
    }

    public function getDataForSetter(
        Concrete $element,
        Data $fieldDefinition,
        string $key,
        array $data,
        UserInterface $user,
        ?FieldContextData $contextData = null,
        bool $isPatch = false,
    ): mixed {
        if (!$fieldDefinition instanceof ProductQuantityPriceRules) {
            return null;
        }

        if (!isset($data[$key])) {
            return [];
        }

        // The data from frontend is in format { rules: [...] }
        $rulesData = $data[$key];
        if (isset($rulesData['rules'])) {
            $rulesData = $rulesData['rules'];
        }

        if (!is_array($rulesData)) {
            return [];
        }

        return $fieldDefinition->getDataFromEditmode($rulesData, $element);
    }

    /**
     * @return array<string>
     */
    private function getConfigActions(): array
    {
        /** @var array<string, mixed> $actions */
        $actions = $this->parameterBag->get('coreshop.product_quantity_price_rules.actions');

        return array_keys($actions);
    }

    /**
     * @return array<string>
     */
    private function getConfigConditions(): array
    {
        /** @var array<string, mixed> $conditions */
        $conditions = $this->parameterBag->get('coreshop.product_quantity_price_rules.conditions');

        return array_keys($conditions);
    }

    /**
     * @return array<string, string>
     */
    private function getConditionSchemaByType(): array
    {
        return $this->schemaCollector->collectSchemasWithTypeMap(
            $this->conditionFormRegistry,
            $this->getConfigConditions(),
        )['schemaByType'];
    }
}
