<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\CoreExtension;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\ProductQuantityPriceRuleType;
use CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject\DISetStateTrait;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use CoreShop\Component\ProductQuantityPriceRules\Repository\ProductQuantityPriceRuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

class ProductQuantityPriceRules extends Data implements Data\CustomResourcePersistingInterface
{
    use DISetStateTrait;

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

    /**
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param ProductQuantityPriceRuleRepositoryInterface $repository
     * @param SerializerInterface $serializer
     * @param array $configActions
     * @param array $configConditions
     * @param array $configCalculators
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProductQuantityPriceRuleRepositoryInterface $repository,
        SerializerInterface $serializer,
        array $configActions,
        array $configConditions,
        array $configCalculators
    ) {
        $this->entityManager($entityManager);
        $this->formFactory($formFactory);
        $this->repository($repository);
        $this->serializer($serializer);
        $this->configActions($configActions);
        $this->configConditions($configConditions);
        $this->configCalculators($configCalculators);
    }

    private function entityManager(EntityManagerInterface $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function formFactory(FormFactoryInterface $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function repository(ProductQuantityPriceRuleRepositoryInterface $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function serializer(SerializerInterface $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function configActions(array $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function configConditions(array $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
    }

    private function configCalculators(array $newValue = null)
    {
        static $value;

        if ($newValue !== null) {
            $value = $newValue;
        }

        return $value;
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
            return null;
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

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        $this->markAsLoaded($object);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return ProductQuantityPriceRuleInterface[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $calculationBehaviourTypes = [];
        $pricingBehaviourTypes = [];

        foreach ($this->configCalculators() as $type) {
            $calculationBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_calculator_' . strtolower($type)];
        }
        foreach ($this->configActions() as $type) {
            $pricingBehaviourTypes[] = [$type, 'coreshop_product_quantity_price_rules_behaviour_' . strtolower($type)];
        }

        $data = [
            'conditions' => array_keys($this->configConditions()),
            'actions' => array_keys($this->configActions()),
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
            $quantityPriceRules = $this->load($object, ['force' => true]);
            $serializedData = $this->serializer()->serialize($quantityPriceRules, 'json', $context);
            $data['rules'] = json_decode($serializedData, true);
        }

        return $data;
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

        if ($data && $object instanceof Concrete) {
            foreach ($data as $rule) {
                $ruleId = isset($rule['id']) && is_numeric($rule['id']) ? $rule['id'] : null;

                $storedRule = null;
                $ruleData = null;

                if ($ruleId !== null) {
                    $storedRule = $this->repository()->find($ruleId);
                }

                if ($storedRule instanceof ProductQuantityPriceRuleInterface) {
                    $ruleData = $this->checkForRangeOrphans($storedRule, $rule);
                }

                $form = $this->formFactory()->createNamed('', ProductQuantityPriceRuleType::class, $ruleData);

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
        if ($object instanceof QuantityRangePriceAwareInterface) {
            if (!$object instanceof Concrete) {
                return;
            }

            $existingQuantityPriceRules = $object->getObjectVar($this->getName());
            $all = $this->load($object, ['force' => true]);
            $founds = [];

            if (is_array($existingQuantityPriceRules)) {
                foreach ($existingQuantityPriceRules as $quantityPriceRule) {
                    if ($quantityPriceRule instanceof ProductQuantityPriceRuleInterface) {
                        $quantityPriceRule->setProduct($object->getId());

                        $this->entityManager()->persist($quantityPriceRule);

                        $founds[] = $quantityPriceRule->getId();
                    }
                }
            }

            foreach ($all as $quantityPriceRule) {
                if (!in_array($quantityPriceRule->getId(), $founds)) {
                    $this->entityManager()->remove($quantityPriceRule);
                }
            }

            $this->entityManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->repository()->findForProduct($object);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        if ($object instanceof QuantityRangePriceAwareInterface) {
            $all = $this->load($object, ['force' => true]);

            foreach ($all as $quantityPriceRule) {
                $this->entityManager()->remove($quantityPriceRule);
            }

            $this->entityManager()->flush();
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
    protected function checkForRangeOrphans(ProductQuantityPriceRuleInterface $storedRule, array $currentRule)
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
            $this->entityManager()->remove($invalidatedRange);
            $storedRule->removeRange($invalidatedRange);
        }

        $this->entityManager()->flush();
        $this->entityManager()->refresh($storedRule);

        return $storedRule;
    }
}
