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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tool;

use CoreShop\Model\Configuration;
use Pimcore\Model\Object\AbstractObject;
use CoreShop\Model\Product;
use CoreShop\Model\BrickVariant;
use CoreShop\Exception;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Classificationstore;
use Pimcore\Model\Object\Classificationstore\KeyConfig;
use Pimcore\Model\Object\Concrete;

/**
 * Class Service
 * @package CoreShop\Tool
 */
class Service
{
    /**
     * allowed elements to display in variants.
     *
     * @var array
     */
    private static $allowedVariationTypes = ['input', 'numeric', 'checkbox', 'select', 'slider', 'href', 'objects'];

    /**
     * @param Product $master
     * @param Product $currentProduct
     * @param string $type
     * @param string $field
     * @param string $language
     * @return array
     */
    public static function getProductVariations(Product $master, Product $currentProduct, $type = 'objectbricks', $field = 'variants', $language = 'en')
    {
        $baseData = [
            "compare" => [],
            "urls" => []
        ];

        switch ($type) {
            case 'objectbricks':
                $baseData = self::getVariantValuesFromBrick($master, $field, $language);
                break;

            case 'classificationstore':
                $baseData = self::getVariantValuesFromClassificationStore($master, $field, $language);
                break;
        }

        $compareValues = $baseData["compare"];
        $variantUrls = $baseData["urls"];

        $projectId = $currentProduct->getId();
        $filtered = $compareValues;

        foreach ($compareValues as $variantName => $variantValues) {
            $currentVariantName = isset($variantValues[ $projectId ]) ? $variantValues[ $projectId ] : null;

            if (is_null($currentVariantName)) {
                continue;
            }

            $tmpArray = $compareValues;
            unset($tmpArray[ $variantName ]);

            $available = self::findProjectIDsInVariant($currentVariantName, $variantValues);
            $filtered = self::findInOthers($tmpArray, $available, $filtered);
        }

        $orderedData = [];

        if (!empty($filtered)) {
            foreach ($filtered as $variantName => $variantValues) {
                $currentVariantName = isset($variantValues[ $projectId ]) ? $variantValues[ $projectId ] : null;

                $variantSelections = [
                    'variantName' => preg_replace('/__(.*?)__/', '', $variantName),
                    'variantValues' => [],
                ];

                $variantValues = array_unique($variantValues);

                if (!empty($variantValues)) {
                    foreach ($variantValues as $pid => $variantValue) {
                        $variantSelections['variantValues'][] = [
                            'productId' => $pid,
                            'productName' => isset($variantUrls[ $pid ]) ?  $variantUrls[ $pid ] : null,
                            'selected' => $currentVariantName === $variantValue,
                            'variantName' => $variantValue,
                        ];
                    }
                }

                if (!empty($variantSelections['variantValues'])) {
                    $orderedData[ strtolower($variantName) ] = $variantSelections;
                }
            }
        }

        return $orderedData;
    }

    /**
     * @param Product $master
     * @param string $classificationStoreField
     * @param string $language
     * @return array
     */
    public static function getVariantValuesFromClassificationStore(Product $master, $classificationStoreField = 'classificationStore', $language = 'en')
    {
        $productClass = Product::getPimcoreObjectClass();
        $productClassDefinition = ClassDefinition::getById($productClass::classId());

        $definition = $productClassDefinition->getFieldDefinition($classificationStoreField);

        if ($definition instanceof ClassDefinition\Data\Classificationstore) {
            $productVariants = self::getAllChildren($master);
            $variantsAndMaster = array_merge([$master], $productVariants);
            $getter = "get" . ucfirst($classificationStoreField);

            $storeId = $definition->getStoreId();

            $groups = new Classificationstore\GroupConfig\Listing();
            $groups->setCondition("storeId = ?", $storeId);
            $groups = $groups->load();
            $dimensionInfo = [];

            foreach ($groups as $groupConfig) {
                $list = new Classificationstore\KeyGroupRelation\Listing();
                $list->setCondition("groupId = ?", $groupConfig->getId());
                $relations = $list->load();

                foreach ($relations as $relation) {
                    $keyConfig = KeyConfig::getById($relation->getKeyId());

                    $dimensionInfo[$groupConfig->getId() . $keyConfig->getId()] = self::getClassificationValidMethods($groupConfig, $keyConfig);
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
                        $value = null;

                        if ($classificationStoreData instanceof Classificationstore) {
                            $value = $classificationStoreData->getLocalizedKeyValue($keyData['groupId'], $keyData['keyId'], $language);

                            if (is_null($value)) {
                                continue;
                            }

                            //Add a namespace, so fields from different blocks can have same name!
                            $secureNameSpace = '__' . $keyData['groupId'].$keyData['keyId'].'__';
                            $variantName = $keyData['name'];

                            $compareValues[$secureNameSpace . $variantName][$productId] = $value;
                            $variantUrls[$productVariant->getId()] = $productVariant->getName();
                        }
                    }
                }
            }

            return [
                "compare" => $compareValues,
                "urls" => $variantUrls
            ];
        }

        return [];
    }

    /**
     * get data for variants from a brick-field
     *
     * @param Product $master
     * @param string $brickField
     * @param string $language
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getVariantValuesFromBrick(Product $master, $brickField = 'variants', $language = 'en')
    {
        $productClass = Product::getPimcoreObjectClass();
        $productClassDefinition = ClassDefinition::getById($productClass::classId());

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
                        if (!$getter instanceof BrickVariant) {
                            throw new Exception('Objectbrick "'.$dimensionGetter.'" needs to be a instance of \CoreShop\Model\BrickVariant"');
                        } elseif (!method_exists($getter, 'getValueForVariant')) {
                            throw new Exception('Variant Class needs a implemented "getValueForVariant" Method.');
                        } else {
                            foreach ($dimensionMethodData as $dMethod) {
                                $variantValue = $getter->getValueForVariant($dMethod, $language);
                                $variantName = $getter->getNameForVariant($dMethod);

                                if ($variantValue === false) {
                                    continue;
                                }

                                if (!is_string($variantValue) && !is_numeric($variantValue)) {
                                    throw new Exception('Variant return value needs to be string or numeric, '.gettype($variantValue).' given.');
                                }

                                //Add a namespace, so fields from different blocks can have same name!
                                $secureNameSpace = '__'.$getter->getType().'__';

                                $compareValues[ $secureNameSpace.$variantName ][ $productId ] = $variantValue;
                                $variantUrls[ $productVariant->getId() ] = $productVariant->getName();
                            }
                        }
                    }
                }
            }

            return [
                "compare" => $compareValues,
                "urls" => $variantUrls
            ];
        }

        return [];
    }

    /**
     * Copy all fields $from to $to
     *
     * @param Concrete $from
     * @param Concrete $to
     */
    public static function copyObject(Concrete $from, Concrete $to)
    {
        //load all in case of lazy loading fields
        $toFd = $to->getClass()->getFieldDefinitions();

        foreach ($toFd as $def) {
            $fromGetter = "get" . ucfirst($def->getName());
            $toSetter = "set" . ucfirst($def->getName());

            if (method_exists($from, $fromGetter) && method_exists($to, $toSetter)) {
                $to->$toSetter($from->$fromGetter());
            }
        }
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
                    if (isset($filtered[ $variantName ])) {
                        unset($filtered[ $variantName ][ $productId ]);
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
        $v = [];

        foreach ($array as $projectID => $variantName) {
            if ($variantName == $value) {
                $v[] = $projectID;
            }
        }

        return $v;
    }

    /**
     * @param Classificationstore\GroupConfig $group
     * @param KeyConfig $field
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

            if ($restrictTypes == true) {
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
     * @param Product $object
     *
     * @return mixed
     */
    private static function getAllChildren(Product $object)
    {
        $list = Product::getList();

        $condition = 'o_path LIKE ?';
        $conditionParams = [$object->getFullPath() . '/%'];
        
        if (Configuration::multiShopEnabled()) {
            $shopParams = [];

            foreach ($object->getShops() as $shop) {
                $shopParams[] = "shops LIKE '%,".$shop.",%'";
            }

            $condition .= " AND (" . implode(" OR ", $shopParams) . ")";
        }
        
        $list->setCondition($condition, $conditionParams);
        $list->setOrderKey('o_key');
        $list->setOrder('asc');
        $list->setObjectTypes([AbstractObject::OBJECT_TYPE_VARIANT]);

        return $list->load();
    }
}
