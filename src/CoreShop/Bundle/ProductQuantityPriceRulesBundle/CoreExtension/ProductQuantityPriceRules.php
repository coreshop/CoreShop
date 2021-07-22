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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\CoreExtension;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Event\ProductQuantityPriceRuleValidationEvent;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\ProductQuantityPriceRuleType;
use CoreShop\Bundle\ResourceBundle\CoreExtension\TempEntityManagerTrait;
use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityMerger;
use CoreShop\Component\ProductQuantityPriceRules\Events;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Repository\ProductQuantityPriceRuleRepositoryInterface;
use CoreShop\Component\Resource\Factory\RepositoryFactoryInterface;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

class ProductQuantityPriceRules extends Data implements
    Data\CustomResourcePersistingInterface,
    Data\CustomVersionMarshalInterface
{
    use TempEntityManagerTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductQuantityPriceRules';

    /**
     * @var int
     */
    public $height;

    public function getParameterTypeDeclaration(): ?string
    {
        return 'array';
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return 'array';
    }

    public function getPhpdocInputType(): ?string
    {
        return 'array';
    }

    public function getPhpdocReturnType(): ?string
    {
        return 'array';
    }

    /**
     * @param mixed $object
     *
     * @return ProductQuantityPriceRuleInterface[]
     */
    public function preGetData($object)
    {
        Assert::isInstanceOf($object, QuantityRangePriceAwareInterface::class);

        if (!$object instanceof Concrete) {
            return [];
        }

        $data = $object->getObjectVar($this->getName());

        if (!$object->isLazyKeyLoaded($this->getName())) {
            $data = $this->load($object, ['force' => true]);

            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($object, $setter)) {
                $object->$setter($data);
            }
        }

        return $data;
    }

    public function preSetData($object, $data, $params = [])
    {
        $this->markAsLoaded($object);

        return $data;
    }

    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    public function marshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return null;
        }

        $serialized = [];

        foreach ($data as $datum) {
            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Version']);

            $serialized[] = $this->getSerializer()->toArray($datum, $context);
        }

        return $serialized;
    }

    public function unmarshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return null;
        }

        $entities = [];
        $tempEntityManager = $this->createTempEntityManager($this->getEntityManager());

        foreach ($data as $storeData) {
            if (!is_array($storeData)) {
                continue;
            }

            $context = DeserializationContext::create();
            $context->setGroups(['Version']);
            $context->setAttribute('em', $tempEntityManager);

            $data = $this->getSerializer()->fromArray($storeData, $this->getProductQuantityPriceRuleRepository()->getClassName(), $context);

            $entities[] = $data;
        }

        return $entities;
    }

    public function marshalRecycleData($object, $data)
    {
        return $this->marshalVersion($object, $data);
    }

    public function unmarshalRecycleData($object, $data)
    {
        return $this->unmarshalVersion($object, $data);
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return array
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $calculationBehaviourTypes = [];
        $pricingBehaviourTypes = [];

        foreach ($this->getContainer()->getParameter('coreshop.product_quantity_price_rules.calculators') as $type) {
            $calculationBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_calculator_' . strtolower($type)];
        }
        foreach ($this->getContainer()->getParameter('coreshop.product_quantity_price_rules.actions') as $type) {
            $pricingBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_behaviour_' . strtolower($type)];
        }

        $serializedData = [
            'conditions' => array_keys($this->getConfigConditions()),
            'actions' => array_keys($this->getConfigActions()),
            'rules' => [],
            'stores' => [
                'calculationBehaviourTypes' => $calculationBehaviourTypes,
                'pricingBehaviourTypes' => $pricingBehaviourTypes,
            ],
        ];

        if ($object instanceof QuantityRangePriceAwareInterface) {
            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);

            $serializedData['rules'] = $this->getSerializer()->toArray($data, $context);
        }

        return $serializedData;
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];
        $errors = [];

        if (!is_array($data) || !$object instanceof Concrete) {
            return $prices;
        }

        $tempEntityManager = $this->createTempEntityManager($this->getEntityManager());
        $specificPriceRuleRepository = $this->getProductQuantityPriceRuleRepositoryFactory()->createNewRepository($tempEntityManager);

        $event = new ProductQuantityPriceRuleValidationEvent($object, $data);
        $this->getEventDispatcher()->dispatch($event, Events::RULES_DATA_FROM_EDITMODE_VALIDATION);

        foreach ($event->getData() as $rule) {

            $storedRule = null;
            $ruleData = null;

            $ruleId = isset($rule['id']) && is_numeric($rule['id']) ? $rule['id'] : null;

            if ($ruleId !== null) {
                $storedRule = $specificPriceRuleRepository->find($ruleId);
            }

            if ($storedRule instanceof ProductQuantityPriceRuleInterface) {
                $ruleData = $this->checkForRangeOrphans($tempEntityManager, $storedRule, $rule);
            }

            $form = $this->getFormFactory()->createNamed('', ProductQuantityPriceRuleType::class, $ruleData);

            $form->submit($rule);

            if ($form->isValid()) {
                $formData = $form->getData();
                $formData->setProduct($object->getId());
                $prices[] = $formData;
            } else {
                foreach ($form->getErrors(true, true) as $e) {
                    $errorMessageTemplate = $e->getMessageTemplate();
                    foreach ($e->getMessageParameters() as $key => $value) {
                        $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
                    }

                    $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
                }

                throw new \Exception(implode(PHP_EOL, $errors));
            }
        }

        return $prices;
    }

    /**
     * @param mixed $object
     * @param array $params
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object, $params = [])
    {
        if (!$object instanceof QuantityRangePriceAwareInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $entityMerger = new EntityMerger($this->getEntityManager());
        $existingQuantityPriceRules = $object->getObjectVar($this->getName());
        $all = $this->load($object, ['force' => true]);
        $founds = [];

        if (is_array($existingQuantityPriceRules)) {
            foreach ($existingQuantityPriceRules as $quantityPriceRule) {
                if ($quantityPriceRule instanceof ProductQuantityPriceRuleInterface) {
                    $quantityPriceRule->setProduct($object->getId());

                    $entityMerger->merge($quantityPriceRule);
                    $this->getEntityManager()->persist($quantityPriceRule);

                    $founds[] = $quantityPriceRule->getId();
                }
            }
        }

        foreach ($all as $quantityPriceRule) {
            if (!in_array($quantityPriceRule->getId(), $founds)) {
                $this->getEntityManager()->remove($quantityPriceRule);
            }
        }

        $this->getEntityManager()->flush();
    }

    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getProductQuantityPriceRuleRepository()->findForProduct($object);
        }

        return null;
    }

    public function delete($object, $params = [])
    {
        if ($object instanceof QuantityRangePriceAwareInterface) {
            $all = $this->load($object, ['force' => true]);

            foreach ($all as $quantityPriceRule) {
                $this->getEntityManager()->remove($quantityPriceRule);
            }

            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param mixed $data
     * @param null  $relatedObject
     * @param mixed $params
     * @param null  $idMapper
     *
     * @return ProductQuantityPriceRuleInterface[]
     *
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[] $array
     *
     * @return array
     */
    protected function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->arrayCastRecursive($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = $this->arrayCastRecursive((array) $value);
                }
            }
        }
        if ($array instanceof \stdClass) {
            return $this->arrayCastRecursive((array) $array);
        }

        return $array;
    }

    /**
     * @param Concrete $object
     */
    protected function markAsLoaded($object)
    {
        if (!$object instanceof Concrete) {
            return;
        }

        $object->markLazyKeyAsLoaded($this->getName());
    }

    /**
     * @param ProductQuantityPriceRuleInterface $storedRule
     * @param array                             $currentRule
     *
     * @return ProductQuantityPriceRuleInterface
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkForRangeOrphans(EntityManager $entityManager, ProductQuantityPriceRuleInterface $storedRule, array $currentRule)
    {
        if ($storedRule->getRanges()->isEmpty()) {
            return $storedRule;
        }

        $currentRanges = isset($currentRule['ranges']) && is_array($currentRule['ranges']) ? $currentRule['ranges'] : [];

        $keepIds = [];
        foreach ($currentRanges as $currentRange) {
            if (isset($currentRange['id']) && $currentRange['id'] !== null) {
                $keepIds[] = (int) $currentRange['id'];
            }
        }

        $invalidatedRanges = $storedRule->getRanges()->filter(function (QuantityRangeInterface $priceRange) use ($keepIds) {
            return !in_array($priceRange->getId(), $keepIds);
        });

        if ($invalidatedRanges->isEmpty()) {
            return $storedRule;
        }

        foreach ($invalidatedRanges as $invalidatedRange) {
            $entityManager->remove($invalidatedRange);
            $storedRule->removeRange($invalidatedRange);
        }

        $entityManager->flush();
        $entityManager->refresh($storedRule);

        return $storedRule;
    }

    public function getForCsvExport($object, $params = [])
    {
        return '';
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return \Pimcore::getContainer()->get('event_dispatcher');
    }

    /**
     * @return ProductQuantityPriceRuleRepositoryInterface
     */
    private function getProductQuantityPriceRuleRepository()
    {
        return $this->getContainer()->get('coreshop.repository.product_quantity_price_rule');
    }
    /**
     * @return RepositoryFactoryInterface
     */
    private function getProductQuantityPriceRuleRepositoryFactory()
    {
        return $this->getContainer()->get('coreshop.repository.factory.product_quantity_price_rule');
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactory()
    {
        return $this->getContainer()->get('form.factory');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function getSerializer()
    {
        return $this->getContainer()->get('jms_serializer');
    }

    /**
     * @return array
     */
    private function getConfigConditions()
    {
        return $this->getContainer()->getParameter('coreshop.product_quantity_price_rules.conditions');
    }

    /**
     * @return array
     */
    private function getConfigActions()
    {
        return $this->getContainer()->getParameter('coreshop.product_quantity_price_rules.actions');
    }
}
