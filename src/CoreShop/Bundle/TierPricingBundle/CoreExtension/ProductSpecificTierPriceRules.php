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

namespace CoreShop\Bundle\TierPricingBundle\CoreExtension;

use CoreShop\Bundle\TierPricingBundle\Form\Type\ProductSpecificTierPriceRuleType;
use CoreShop\Component\Pimcore\BCLayer\CustomResourcePersistingInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Repository\ProductSpecificTierPriceRuleRepositoryInterface;
use JMS\Serializer\SerializationContext;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

class ProductSpecificTierPriceRules extends Data implements CustomResourcePersistingInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductSpecificTierPriceRules';

    /**
     * @var int
     */
    public $height;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer()
    {
        return \Pimcore::getContainer();
    }

    /**
     * @return ProductSpecificTierPriceRuleRepositoryInterface
     */
    private function getProductSpecificTierPriceRuleRepository()
    {
        return $this->getContainer()->get('coreshop.repository.product_specific_tier_price_rule');
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
     * @return \Doctrine\ORM\EntityManager
     */
    private function getRangeEntityManager()
    {
        return $this->getContainer()->get('coreshop.manager.product_tier_price_range');
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
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
        return $this->getContainer()->getParameter('coreshop.product_specific_tier_price_rule.conditions');
    }

    /**
     * @return array
     */
    private function getConfigActions()
    {
        return $this->getContainer()->getParameter('coreshop.product_tier_price.actions');
    }

    /**
     * @param mixed $object
     *
     * @return ProductSpecificTierPriceRuleInterface[]
     */
    public function preGetData($object)
    {
        Assert::isInstanceOf($object, ProductInterface::class);

        //TODO: Remove once CoreShop requires min Pimcore 5.5
        if (method_exists($object, 'getObjectVar')) {
            $data = $object->getObjectVar($this->getName());
        } else {
            $data = $object->{$this->getName()};
        }

        if (!method_exists($object, 'getO__loadedLazyFields')) {
            return $data;
        }

        if (!in_array($this->getName(), $object->getO__loadedLazyFields())) {
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
        if (!in_array($this->getName(), $object->getO__loadedLazyFields())) {
            $object->addO__loadedLazyField($this->getName());
        }

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
     * @return ProductSpecificTierPriceRuleInterface[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $data = [
            'conditions' => array_keys($this->getConfigConditions()),
            'actions' => array_keys($this->getConfigActions()),
            'rules' => [],
        ];

        if ($object instanceof ProductInterface) {
            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);
            $tierPrices = $this->load($object, ['force' => true]);
            $serializedData = $this->getSerializer()->serialize($tierPrices, 'json', $context);
            $data['rules'] = json_decode($serializedData, true);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return array|mixed
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
                    $storedRule = $this->getProductSpecificTierPriceRuleRepository()->find($ruleId);
                }

                if ($storedRule instanceof ProductSpecificTierPriceRuleInterface) {
                    $ruleData = $this->checkForRangeOrphans($storedRule, $rule);
                }

                $form = $this->getFormFactory()->createNamed('', ProductSpecificTierPriceRuleType::class, $ruleData);

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
     * @param       $object
     * @param array $params
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            //TODO: Remove once CoreShop requires min Pimcore 5.5
            if (method_exists($object, 'getObjectVar')) {
                $existingTierPriceRules = $object->getObjectVar($this->getName());
            } else {
                $existingTierPriceRules = $object->{$this->getName()};
            }

            $all = $this->load($object, ['force' => true]);
            $founds = [];

            if (is_array($existingTierPriceRules)) {
                foreach ($existingTierPriceRules as $tierPrice) {
                    if ($tierPrice instanceof ProductSpecificTierPriceRuleInterface) {
                        $tierPrice->setProduct($object->getId());

                        $this->getEntityManager()->persist($tierPrice);

                        $founds[] = $tierPrice->getId();
                    }
                }
            }

            foreach ($all as $tierPrice) {
                if (!in_array($tierPrice->getId(), $founds)) {
                    $this->getEntityManager()->remove($tierPrice);
                }
            }

            $this->getEntityManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getProductSpecificTierPriceRuleRepository()->findForProduct($object);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $all = $this->load($object, ['force' => true]);

            foreach ($all as $tierPrice) {
                $this->getEntityManager()->remove($tierPrice);
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
     * @return ProductSpecificTierPriceRuleInterface[]
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
     * @param ProductSpecificTierPriceRuleInterface $storedRule
     * @param array                                 $currentRule
     *
     * @return ProductSpecificTierPriceRuleInterface
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkForRangeOrphans(ProductSpecificTierPriceRuleInterface $storedRule, array $currentRule)
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

        $invalidatedRanges = $storedRule->getRanges()->filter(function (ProductTierPriceRangeInterface $priceRange) use ($keepIds) {
            return !in_array($priceRange->getId(), $keepIds);
        });

        if ($invalidatedRanges->isEmpty()) {
            return $storedRule;
        }

        foreach ($invalidatedRanges as $invalidatedRange) {
            $this->getRangeEntityManager()->remove($invalidatedRange);
            $storedRule->removeRange($invalidatedRange);
        }

        $this->getRangeEntityManager()->flush();
        $this->getEntityManager()->refresh($storedRule);

        return $storedRule;
    }
}
