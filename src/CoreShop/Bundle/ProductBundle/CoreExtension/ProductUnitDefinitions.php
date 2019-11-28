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

use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionsType;
use CoreShop\Component\Pimcore\BCLayer\CustomResourcePersistingInterface;
use CoreShop\Component\Pimcore\BCLayer\CustomVersionMarshalInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use CoreShop\Component\Product\Repository\ProductUnitDefinitionsRepositoryInterface;
use Doctrine\ORM\UnitOfWork;
use JMS\Serializer\SerializationContext;
use Pimcore\Model;
use Pimcore\Model\DataObject\LazyLoadedFieldsInterface;

class ProductUnitDefinitions extends Model\DataObject\ClassDefinition\Data implements CustomResourcePersistingInterface, CustomVersionMarshalInterface
{
    /**
     * @var string
     */
    public $fieldtype = 'coreShopProductUnitDefinitions';

    /**
     * @var float
     */
    public $width;

    /**
     * @var int
     */
    public $defaultValue;

    /**
     * @var string
     */
    public $phpdocType = 'array';

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $this->getAsIntegerCast($width);

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultValue()
    {
        if ($this->defaultValue !== null) {
            return $this->toNumeric($this->defaultValue);
        }

        return 0;
    }

    /**
     * @param int $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        if (strlen(strval($defaultValue)) > 0) {
            $this->defaultValue = $defaultValue;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryColumnType()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnType()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Get ' . str_replace(['/**', '*/', '//'], '', $this->getName()) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '*' . "\n";
        $code .= '* @return null|' . $this->getPhpdocType() . '|\CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function get' . ucfirst($key) . ' () {' . "\n";
        $code .= "\t" . '$this->' . $key . ' = $this->getClass()->getFieldDefinition("' . $key . '")->preGetData($this);' . "\n";
        $code .= "\t" . '$data = $this->' . $key . ";\n";
        $code .= "\t" . 'if(\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("' . $key . '")->isEmpty($data)) {' . "\n";
        $code .= "\t\t" . 'try {' . "\n";
        $code .= "\t\t\t" . 'return $this->getValueFromParent("' . $key . '");' . "\n";
        $code .= "\t\t" . '} catch (InheritanceParentNotFoundException $e) {' . "\n";
        $code .= "\t\t\t" . '// no data from parent available, continue ... ' . "\n";
        $code .= "\t\t" . '}' . "\n";
        $code .= "\t" . '}' . "\n";
        $code .= "\t" . 'return $data;' . "\n";
        $code .= "}\n\n";

        return $code;
    }

    public function getSetterCode($class)
    {
        $key = $this->getName();
        $code = '/**' . "\n";
        $code .= '* Set ' . str_replace(['/**', '*/', '//'], '', $key) . ' - ' . str_replace(['/**', '*/', '//'], '', $this->getTitle()) . "\n";
        $code .= '*' . "\n";
        $code .= '* @param null|\CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface $unitDefinitions' . "\n";
        $code .= '*' . "\n";
        $code .= '* @return static' . "\n";
        $code .= '*/' . "\n";
        $code .= 'public function set' . ucfirst($key) . ' (\CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface $unitDefinitions = null) {' . "\n";
        $code .= "\t" . '$this->' . $key . ' = $unitDefinitions;' . "\n";
        $code .= "\t" . '$this->' . $key . ' = ' . '$this->getClass()->getFieldDefinition("' . $key . '")->preSetData($this, $this->' . $key . ');' . "\n";
        $code .= "\t" . 'return $this;' . "\n";
        $code .= "}\n\n";

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function marshalVersion($object, $data)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function unmarshalVersion($object, $data)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function preGetData($object, $params = [])
    {
        /**
         * @var Model\DataObject\Concrete $object
         */
        $data = $object->getObjectVar($this->getName());

        if (!$object->isLazyKeyLoaded($this->getName())) {
            $data = $this->load($object, ['force' => true]);

            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($object, $setter)) {
                $object->$setter($data);
            }
        }

        if ($data instanceof ProductUnitDefinitionsInterface) {
            if ($this->getEntityManager()->getUnitOfWork()->getEntityState($data, UnitOfWork::STATE_NEW) === UnitOfWork::STATE_NEW) {
                $data->setProduct($object);
                $data = $this->getEntityManager()->merge($data);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        if ($object instanceof LazyLoadedFieldsInterface) {
            $object->markLazyKeyAsLoaded($this->getName());
        }

        if ($data instanceof ProductUnitDefinitionsInterface) {
            if ($object instanceof ProductInterface) {
                $data->setProduct($object);
            }

            $this->getEntityManager()->persist($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getProductUnitDefinitionsRepository()->findOneForProduct($object);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $params = [])
    {
        if (!$object instanceof ProductInterface) {
            return;
        }

        if (!$object instanceof Model\DataObject\Concrete) {
            return;
        }

        $productUnitDefinitions = $object->getObjectVar($this->getName());

        if ($productUnitDefinitions instanceof ProductUnitDefinitionsInterface) {
            $productUnitDefinitions->setProduct($object);

            $this->getEntityManager()->persist($productUnitDefinitions);
            $this->getEntityManager()->flush($productUnitDefinitions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
    {
        if (!$object instanceof ProductInterface) {
            return;
        }

        $productUnitDefinitions = $this->load($object, ['force' => true]);
        if ($productUnitDefinitions === null) {
            return;
        }

        $this->getEntityManager()->remove($productUnitDefinitions);
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if (!$object instanceof ProductInterface) {
            return [];
        }

        if (null === $data) {
            return [];
        }

        $productUnitDefinition = $this->getProductUnitDefinitionsRepository()->findOneForProduct($object);

        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->setGroups(['Default', 'Detailed']);

        return $this->getSerializer()->toArray($productUnitDefinition, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return null;
        }

        $errors = [];
        $productUnitDefinitionsValues = null;

        $unitDefinitionsEntity = null;
        $unitDefinitionsId = isset($data['id']) && is_numeric($data['id']) ? $data['id'] : null;

        if ($unitDefinitionsId !== null) {
            $unitDefinitionsEntity = $this->getProductUnitDefinitionsRepository()->findOneForProduct($object);
        }

        $form = $this->getFormFactory()->createNamed('', ProductUnitDefinitionsType::class, $unitDefinitionsEntity);

        $parsedData = $this->expandDotNotationKeys($data);
        $parsedData['product'] = $object->getId();

        $form->submit($parsedData);

        if ($form->isValid()) {
            $productUnitDefinitionsValues = $form->getData();
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

        return $productUnitDefinitionsValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getForCsvExport($object, $params = [])
    {
        $data = $this->getDataFromObjectParam($object, $params);

        if (!is_array($data) || empty($data)) {
            return '{}';
        }

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCsvImport($importValue, $object = null, $params = [])
    {
        if (!$object) {
            throw new \Exception('This version of Pimcore is not supported for product unit definitions import.');
        }

        $data = $importValue == '' ? [] : json_decode($importValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(sprintf('Error decoding Product Unit Definitions JSON `%s`: %s', $importValue, json_last_error_msg()));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * @param mixed $value
     *
     * @return float|int
     */
    protected function toNumeric($value)
    {
        if (strpos((string) $value, '.') === false) {
            return (int) $value;
        }

        return (float) $value;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function expandDotNotationKeys(array $array)
    {
        $result = [];

        while (count($array)) {
            $value = reset($array);
            $key = key($array);
            unset($array[$key]);

            if (strpos($key, '.') !== false) {
                list($base, $ext) = explode('.', $key, 2);
                if (!array_key_exists($base, $array)) {
                    $array[$base] = [];
                }
                $array[$base][$ext] = $value;
            } elseif (is_array($value)) {
                $result[$key] = $this->expandDotNotationKeys($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return \Pimcore::getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactory()
    {
        return \Pimcore::getContainer()->get('form.factory');
    }

    /**
     * @return ProductUnitDefinitionsRepositoryInterface
     */
    protected function getProductUnitDefinitionsRepository()
    {
        return \Pimcore::getContainer()->get('coreshop.repository.product_unit_definitions');
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function getSerializer()
    {
        return \Pimcore::getContainer()->get('jms_serializer');
    }
}
