<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_ObjectController
 */
class CoreShop_Admin_ObjectController extends Admin
{
    /**
     * @var string
     */
    protected $variantsBrick = 'variants';

    public function init()
    {
        parent::init();
    }

    public function generateVariantsAction()
    {
        $objectId = $this->getParam('objectId');
        $brickType = $this->getParam('brickType');
        $fieldName = $this->getParam('field');
        $values = explode(',', $this->getParam('values'));

        $object = \Pimcore\Model\Object\AbstractObject::getById($objectId);

        if (!$object instanceof \CoreShop\Model\Product) {
            $this->_helper->json(['success' => false, 'message' => 'only Product is allowed']);
        }

        $field = $object->getClass()->getFieldDefinition($this->variantsBrick);

        if (!$field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objectbricks) {
            $this->_helper->json(['success' => false, 'message' => 'variants field has wrong type']);
        }

        $allowedTypes = $field->getAllowedTypes();

        if (!in_array($brickType, $allowedTypes)) {
            $this->_helper->json(['success' => false, 'message' => "$brickType is not allowed"]);
        }

        $definition = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($brickType);

        if ($definition instanceof \Pimcore\Model\Object\Objectbrick\Definition) {
            $fieldDefinition = $definition->getFieldDefinition($fieldName);

            if (!$fieldDefinition instanceof \Pimcore\Model\Object\ClassDefinition\Data) {
                $this->_helper->json(['success' => false, 'message' => "$fieldName not found in brick $brickType"]);
            }
        }

        $className = get_class($object);
        $brickGetter = 'get'.ucfirst($this->variantsBrick);
        $brickTypeSetter = 'set'.ucfirst($brickType);
        $fieldSetter = 'set'.ucfirst($fieldName);

        $brickClass = "\\Pimcore\\Model\\Object\\Objectbrick\\Data\\$brickType";

        foreach ($values as $value) {
            $object = new $className();
            $object->setType('variant');
            $object->setKey(\Pimcore\File::getValidFilename($value));
            $object->setPublished(true);
            $object->setParentId($objectId);

            $brick = new $brickClass($object);
            $brick->$fieldSetter($value);

            $object->$brickGetter()->$brickTypeSetter($brick);

            $object->save();
        }

        $this->_helper->json(['success' => true]);
    }

    public function getVariantBricksAction()
    {
        $id = intval($this->getParam('id'));

        $object = \Pimcore\Model\Object\AbstractObject::getById($id);

        if (!$object instanceof \CoreShop\Model\Product) {
            $this->_helper->json(['success' => false, 'message' => 'only Product is allowed']);
        }

        $field = $object->getClass()->getFieldDefinition($this->variantsBrick);

        if (!$field instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objectbricks) {
            $this->_helper->json(['success' => false, 'message' => 'variants field has wrong type']);
        }

        $allowedTypes = $field->getAllowedTypes();
        $brickFields = [];

        foreach ($allowedTypes as $type) {
            $definition = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($type);

            $brickFields[] = [
                'name' => $definition->getKey(),
            ];
        }

        $this->_helper->json(['success' => true, 'data' => $brickFields]);
    }

    public function getBrickFieldsAction()
    {
        $key = $this->getParam('key');

        $variantFields = [];
        $definition = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($key);

        if ($definition instanceof \Pimcore\Model\Object\Objectbrick\Definition) {
            $fieldsInDefinition = $definition->getFieldDefinitions();

            foreach ($fieldsInDefinition as $field) {
                $variantFields[] = [
                    'type' => $field->getFieldtype(),
                    'name' => $field->getName(),
                ];
            }
        }

        $this->_helper->json(['success' => true, 'data' => $variantFields]);
    }
}
