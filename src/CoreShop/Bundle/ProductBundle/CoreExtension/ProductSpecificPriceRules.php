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

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleType;
use CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject\DISetStateTrait;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\LazyLoadedFieldsInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Webmozart\Assert\Assert;

class ProductSpecificPriceRules extends Data implements Data\CustomResourcePersistingInterface
{
    use DISetStateTrait;

    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductSpecificPriceRules';

    /**
     * @var int
     */
    public $height;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param ProductSpecificPriceRuleRepositoryInterface $repository
     * @param SerializerInterface $serializer
     * @param array $configActions
     * @param array $configConditions
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProductSpecificPriceRuleRepositoryInterface $repository,
        SerializerInterface $serializer,
        array $configActions,
        array $configConditions
    ) {
        $this->entityManager($entityManager);
        $this->formFactory($formFactory);
        $this->repository($repository);
        $this->serializer($serializer);
        $this->configActions($configActions);
        $this->configConditions($configConditions);
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

    private function repository(ProductSpecificPriceRuleRepositoryInterface $newValue = null)
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

    /**
     * @param mixed $object
     *
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function preGetData($object)
    {
        Assert::isInstanceOf($object, ProductInterface::class);

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
     * @param Concrete $object
     * @param mixed    $data
     * @param array    $params
     *
     * @return mixed
     */
    public function preSetData($object, $data, $params = [])
    {
        if ($object instanceof LazyLoadedFieldsInterface) {
            $object->markLazyKeyAsLoaded($this->getName());
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
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        $return = [
            'actions' => array_keys($this->configActions()),
            'conditions' => array_keys($this->configConditions()),
            'rules' => [],
        ];

        if ($object instanceof ProductInterface) {
            $prices = $this->load($object, ['force' => true]);

            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);

            $serializedData = $this->serializer()->serialize($prices, 'json', $context);

            $return['rules'] = json_decode($serializedData, true);
        }

        return $return;
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];
        $errors = [];

        if ($data && $object instanceof Concrete) {
            foreach ($data as $dataRow) {
                $form = $this->formFactory()->createNamed('', ProductSpecificPriceRuleType::class);

                $form->submit($dataRow);

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
     * @param Concrete $object
     * @param array    $params
     */
    public function save($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $existingPriceRules = $object->getObjectVar($this->getName());

            $all = $this->load($object, ['force' => true]);
            $founds = [];

            if (is_array($existingPriceRules)) {
                foreach ($existingPriceRules as $price) {
                    if ($price instanceof ProductSpecificPriceRuleInterface) {
                        $price->setProduct($object->getId());

                        $this->entityManager()->persist($price);
                        $this->entityManager()->flush();

                        $founds[] = $price->getId();
                    }
                }
            }

            foreach ($all as $price) {
                if (!in_array($price->getId(), $founds)) {
                    $this->entityManager()->remove($price);
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
        if ($object instanceof ProductInterface) {
            $all = $this->load($object, ['force' => true]);

            foreach ($all as $price) {
                $this->entityManager()->remove($price);
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
     * @return ProductSpecificPriceRuleInterface[]
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
}
