<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Helper;

use CoreShop\Component\Product\Model\ProductInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;


/**
 * TODO: This class needs some hard refactoring! Currently only copy&paste from Version 1!
 */
class VariantHelper
{
    /**
     * allowed elements to display in variants.
     *
     * @var array
     */
    private static $allowedVariationTypes = ['input', 'numeric', 'checkbox', 'select', 'slider', 'href', 'objects'];

    /**
     * @param ProductInterface $master
     * @param ProductInterface $currentProduct
     * @param string $type
     * @param string $field
     * @param string $language
     *
     * @return array
     */
    public static function getProductVariations(ProductInterface $master, ProductInterface $currentProduct, $type = 'objectbricks', $field = 'variants', $language = 'en')
    {
        $baseData = [
            'compare' => [],
            'urls' => [],
        ];

        switch ($type) {
            case 'objectbricks':
                $baseData = self::getVariantValuesFromBrick($master, $field, $language);
                break;

            case 'classificationstore':
                $baseData = self::getVariantValuesFromClassificationStore($master, $field, $language);
                break;
        }

        $compareValues = $baseData['compare'];
        $variantUrls = $baseData['urls'];

        $projectId = $currentProduct->getId();
        $filtered = $compareValues;

        if (is_array($compareValues)) {
            foreach ($compareValues as $variantName => $variantValues) {
                $currentVariantName = isset($variantValues[$projectId]) ? $variantValues[$projectId] : null;

                if (is_null($currentVariantName)) {
                    continue;
                }

                $tmpArray = $compareValues;
                unset($tmpArray[$variantName]);

                $available = self::findProjectIDsInVariant($currentVariantName, $variantValues);
                $filtered = self::findInOthers($tmpArray, $available, $filtered);
            }
        }

        $orderedData = [];

        if (!empty($filtered)) {
            foreach ($filtered as $variantName => $variantValues) {
                $currentVariantName = isset($variantValues[$projectId]) ? $variantValues[$projectId] : null;

                $variantSelections = [
                    'variantName' => preg_replace('/__(.*?)__/', '', $variantName),
                    'variantValues' => [],
                ];

                $variantValues = array_unique($variantValues);

                if (!empty($variantValues)) {
                    foreach ($variantValues as $pid => $variantValue) {
                        $variantSelections['variantValues'][] = [
                            'productId' => $pid,
                            'productName' => isset($variantUrls[$pid]) ? $variantUrls[$pid] : null,
                            'selected' => $currentVariantName === $variantValue,
                            'variantName' => $variantValue,
                        ];
                    }
                }

                if (!empty($variantSelections['variantValues'])) {
                    $orderedData[strtolower($variantName)] = $variantSelections;
                }
            }
        }

        return $orderedData;
    }

    /**
     * @param ProductInterface $master
     * @param string $classificationStoreField
     * @param string $language
     *
     * @return array
     */
    public static function getVariantValuesFromClassificationStore(ProductInterface $master, $classificationStoreField = 'classificationStore', $language = 'en')
    {
        $productClassDefinition = ClassDefinition::getById($master->getClassId());

        $definition = $productClassDefinition->getFieldDefinition($classificationStoreField);

        if ($definition instanceof ClassDefinition\Data\Classificationstore) {
            $productVariants = self::getAllChildren($master);
            $variantsAndMaster = array_merge([$master], $productVariants);
            $getter = 'get'.ucfirst($classificationStoreField);

            $storeId = $definition->getStoreId();

            $groups = new Classificationstore\GroupConfig\Listing();
            $groups->setCondition('storeId = ?', $storeId);
            $groups = $groups->load();
            $dimensionInfo = [];

            foreach ($groups as $groupConfig) {
                $list = new Classificationstore\KeyGroupRelation\Listing();
                $list->setCondition('groupId = ?', $groupConfig->getId());
                $relations = $list->load();

                foreach ($relations as $relation) {
                    $keyConfig = KeyConfig::getById($relation->getKeyId());

                    $dimensionInfo[$groupConfig->getId().$keyConfig->getId()] = self::getClassificationValidMethods($groupConfig, $keyConfig);
                }
            }

            $compareValues = [];
            $variantUrls = [];

            foreach ($variantsAndMaster as $productVariant) {
                $productId = $productVariant->getId();

                if (!empty($dimensionInfo)) {
                    foreach ($dimensionInfo as $keyName => $keyData) {
                        if (empty($keyData)) {
                            continue;
                        }

                        $classificationStoreData = $productVariant->$getter();

                        if ($classificationStoreData instanceof Classificationstore) {
                            $value = $classificationStoreData->getLocalizedKeyValue($keyData['groupId'], $keyData['keyId'], $language);

                            if (is_null($value)) {
                                continue;
                            }

                            //Add a namespace, so fields from different blocks can have same name!
                            $secureNameSpace = '__'.$keyData['groupId'].$keyData['keyId'].'__';
                            $variantName = $keyData['name'];

                            $compareValues[$secureNameSpace.$variantName][$productId] = $value;
                            $variantUrls[$productVariant->getId()] = $productVariant->getName();
                        }
                    }
                }
            }

            return [
                'compare' => $compareValues,
                'urls' => $variantUrls,
            ];
        }

        return [];
    }

    /**
     * get data for variants from a brick-field.
     *
     * @param ProductInterface $master
     * @param string $brickField
     * @param string $language
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function getVariantValuesFromBrick(ProductInterface $master, $brickField = 'variants', $language = 'en')
    {
        $productClassDefinition = ClassDefinition::getById($master->getClassId());

        $definition = $productClassDefinition->getFieldDefinition($brickField);

        if ($definition instanceof ClassDefinition\Data\Objectbricks) {
            $productVariants = self::getAllChildren($master);
            $variantsAndMaster = array_merge([$master], $productVariants);

            //we do have some dimension entries!
            $variantData = $master->getVariants();

            $dimensionInfo = [];

            if (!is_null($variantData)) {
                $brickGetters = $variantData->getBrickGetters();

                if (!empty($brickGetters)) {
                    foreach ($brickGetters as $brickGetter) {
                        $getter = $variantData->{$brickGetter}();

                        if (!is_null($getter)) {
                            $dimensionMethodData = self::getProductValidMethods($getter);
                            $dimensionInfo[$brickGetter] = $dimensionMethodData;
                        }
                    }
                }
            }

            $compareValues = [];
            $variantUrls = [];

            foreach ($variantsAndMaster as $productVariant) {
                $productId = $productVariant->getId();

                if (!empty($dimensionInfo)) {
                    foreach ($dimensionInfo as $dimensionGetter => $dimensionMethodData) {
                        if (empty($dimensionMethodData)) {
                            continue;
                        }

                        $getter = $productVariant->getVariants()->{$dimensionGetter}();

                        //Getter must be an instance of Variant Model
                        /*if (!$getter instanceof BrickVariant) {
                            throw new \Exception('Objectbrick "'.$dimensionGetter.'" needs to be a instance of \CoreShop\Model\BrickVariant"');
                        } elseif (!method_exists($getter, 'getValueForVariant')) {
                            throw new \Exception('Variant Class needs a implemented "getValueForVariant" Method.');
                        } else {*/
                        foreach ($dimensionMethodData as $dMethod) {
                            $variantValue = $getter->getValueForVariant($dMethod, $language);
                            $variantName = $getter->getNameForVariant($dMethod);

                            if ($variantValue === false) {
                                continue;
                            }

                            if (!is_string($variantValue) && !is_numeric($variantValue)) {
                                throw new \Exception('Variant return value needs to be string or numeric, '.gettype($variantValue).' given.');
                            }

                            //Add a namespace, so fields from different blocks can have same name!
                            $secureNameSpace = '__'.$getter->getType().'__';

                            $compareValues[$secureNameSpace.$variantName][$productId] = $variantValue;
                            $variantUrls[$productVariant->getId()] = $productVariant->getName();
                        }
                        //}
                    }
                }
            }

            return [
                'compare' => $compareValues,
                'urls' => $variantUrls,
            ];
        }

        return [];
    }

    /**
     * @param $tmpArray
     * @param $allowedProductIds
     * @param $filtered
     *
     * @return mixed
     */
    private static function findInOthers($tmpArray, $allowedProductIds, $filtered)
    {
        foreach ($tmpArray as $variantName => $variantValues) {
            foreach ($variantValues as $productId => $variantValue) {
                if (!in_array($productId, $allowedProductIds)) {
                    if (isset($filtered[$variantName])) {
                        unset($filtered[$variantName][$productId]);
                    }
                }
            }
        }

        return $filtered;
    }

    /**
     * @param $value
     * @param $array
     *
     * @return array
     */
    private static function findProjectIDsInVariant($value, $array)
    {
        $values = [];

        foreach ($array as $projectID => $variantName) {
            if ($variantName == $value) {
                $values[] = $projectID;
            }
        }

        return $values;
    }

    /**
     * @param Classificationstore\GroupConfig $group
     * @param KeyConfig $field
     *
     * @return array
     */
    private static function getClassificationValidMethods(Classificationstore\GroupConfig $group, KeyConfig $field)
    {
        if (!in_array($field->getType(), self::$allowedVariationTypes)) {
            return [];
        }

        return [
            'groupId' => $group->getId(),
            'keyId' => $field->getId(),
            'name' => $field->getName(),
            'type' => $field->getType(),
            'title' => $field->getTitle(),
        ];
    }

    /**
     * @param      $getter
     * @param bool $restrictTypes
     *
     * @return array
     */
    private static function getProductValidMethods($getter, $restrictTypes = true)
    {
        $fields = $getter->getDefinition()->getFieldDefinitions();

        if (empty($fields)) {
            return [];
        }

        $validValues = [];

        foreach ($fields as $field) {
            $isValid = false;

            if ($restrictTypes === true) {
                if (in_array($field->getFieldType(), self::$allowedVariationTypes)) {
                    $isValid = true;
                }
            } else {
                $isValid = true;
            }

            if ($isValid) {
                $validValues[] = [
                    'name' => $field->getName(),
                    'type' => $field->getPhpdocType(),
                    'title' => $field->getTitle(),
                ];
            }
        }

        return $validValues;
    }

    /**
     * @param ProductInterface $object
     *
     * @return mixed
     */
    private static function getAllChildren(ProductInterface $object)
    {
        $list = \Pimcore::getContainer()->get('coreshop.repository.product')->getList();

        $condition = 'o_path LIKE ?';
        $conditionParams = [$object->getFullPath().'/%'];

        $storeParams = [];

        foreach ($object->getStores() as $shop) {
            $storeParams[] = "stores LIKE '%,".$shop.",%'";
        }

        $condition .= ' AND ('.implode(' OR ', $storeParams).')';

        $list->setCondition($condition, $conditionParams);
        $list->setOrderKey('o_key');
        $list->setOrder('asc');
        $list->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT]);

        return $list->load();
    }
}
