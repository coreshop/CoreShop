<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tool;

use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\Objectbrick\Definition;

use CoreShop\Model\Product;
use CoreShop\Model\BrickVariant;
use TijsVerkoyen\CssToInlineStyles\Exception;

class Service
{

    private static $allowedVariationTypes = array("input","numeric","checkbox","select","slider", "objects");

    /**
     * @param \CoreShop\Model\Product $master
     * @param \CoreShop\Model\Product $currentProduct
     * @param string                  $language
     *
     * @return array
     * @throws \TijsVerkoyen\CssToInlineStyles\Exception
     */
    public static function getProductVariations( Product $master, Product $currentProduct, $language = 'en' ) {

        $productVariants = self::getAllChildren($master);

        $projectId = $currentProduct->getId();

        $variantsAndMaster = array_merge(array($master), $productVariants);

        //we do have some dimension entries!
        $variantData = $master->getVariants();

        $dimensionInfo = array();
        $variantUrls = array();

        if( !is_null( $variantData ) ) {

            $brickGetters = $variantData->getBrickGetters();

            if( !empty( $brickGetters ) ) {

                foreach( $brickGetters as $brickGetter) {

                    $getter = $variantData->{$brickGetter}();

                    if( !is_null( $getter ) ) {

                        $dimensionMethods = self::getProductValidMethods( $getter );

                        $dimensionInfo[$brickGetter] = $dimensionMethods;

                    }

                }


            }

        }

        $compareValues = array();

        foreach ($variantsAndMaster as $productVariant) {

            $productId = $productVariant->getId();

            if( !empty( $dimensionInfo ) ) {

                foreach( $dimensionInfo as $dimensionGetter => $dimensionMethods ) {

                    if( empty( $dimensionMethods ) )
                        continue;

                    $getter = $productVariant->getVariants()->{$dimensionGetter}();

                    //Getter must be an instance of Variant Model
                    if( !$getter instanceof BrickVariant ) {

                        throw new Exception( 'Objectbrick "' . $dimensionGetter . '" needs to be a instance of \CoreShop\Model\BrickVariant"');

                    } else if( !method_exists( $getter, 'getValueForVariant')) {

                        throw new Exception( 'Variant Class needs a implemented "getValueForVariant" Method.');

                    } else {

                        foreach( $dimensionMethods as $dMethod ) {

                            $variantValue = $getter->getValueForVariant($dMethod,$language );

                            if( !is_string( $variantValue ) ) {

                                throw new Exception( 'Variant return value needs to be string, ' . gettype($variantValue) . ' given.');

                            }

                            //Add a namespace, so fields from different blocks can have same name!
                            $secureNameSpace = '__' . $getter->getType() . '__';

                            $compareValues[ $secureNameSpace . $dMethod ][ $productId ] = $variantValue;
                            $variantUrls[ $productVariant->getId() ] = $productVariant->getName();

                        }

                    }

                }

            }

        }

        $filtered = $compareValues;

        foreach( $compareValues as $variantName => $variantValues ) {

            $currentVariantName = isset( $variantValues[ $projectId ] ) ? $variantValues[ $projectId ] : NULL;

            if( is_null( $currentVariantName ) )
                continue;

            $tmpArray = $compareValues;
            unset( $tmpArray[ $variantName ]);

            $available = self::findProjectIDsInVariant($currentVariantName, $variantValues);

            $filtered = self::findInOthers($tmpArray, $available, $filtered);

        }

        $orderedData = array();

        if( !empty( $filtered ) ) {

            foreach ($filtered as $variantName => $variantValues ) {

                $currentVariantName = isset( $variantValues[ $projectId ] ) ? $variantValues[ $projectId ] : NULL;

                $variantSelections = array(

                    'variantName' =>  preg_replace("/__(.*?)__/", "", $variantName),
                    'variantValues' => array()

                );

                $variantValues = array_unique( $variantValues );

                if( !empty( $variantValues ) ) {

                    foreach( $variantValues as $pid => $variantValue ) {

                        $variantSelections['variantValues'][] = array(

                            'productId' => $pid,
                            'productName' => isset( $variantUrls[ $pid ] ) ?  $variantUrls[ $pid ] : NULL,
                            'selected' => $currentVariantName === $variantValue,
                            'variantName' => $variantValue

                        );

                    }

                }

                if( !empty( $variantSelections['variantValues'] ) ) {

                    $orderedData[ strtolower( $variantName ) ] = $variantSelections;

                }

            }

        }

        return $orderedData;

    }

    private static function findInOthers( $tmpArray, $allowedProductIds, $filtered ) {

        foreach( $tmpArray as $variantName => $variantValues ) {

            foreach ($variantValues as $productId => $variantValue) {

                if( !in_array( $productId, $allowedProductIds ) ) {

                    if( isset( $filtered[ $variantName ] ) ) {

                        unset( $filtered[ $variantName ][ $productId ] );

                    }

                }


            }

        }

        return $filtered;

    }

    private static function findProjectIDsInVariant( $value, $array ) {

        $v=array();

        foreach( $array as $projectID => $variantName) {

            if( $variantName == $value ) {
                $v[]=$projectID;
            }
        }

        return $v;

    }

    private static function getProductValidMethods( $getter, $restrictTypes = TRUE ) {

        $fields = $getter->getDefinition()->getFieldDefinitions();

        if( empty( $fields ) )
            return array();

        $valid = array();

        foreach( $fields as $field ) {

            if( $restrictTypes == TRUE ) {

                if( in_array( $field->getFieldType(), self::$allowedVariationTypes ) )
                    $valid[] = $field->getName();

            } else {

                $valid[] = $field->getName();

            }

        }

        return $valid;

    }

    private static function getAllChildren(AbstractObject $object) {

        $list = new \Pimcore\Model\Object\Listing();

        $list->setCondition("o_path LIKE ?", $object->getFullPath() . "/%");
        $list->setOrderKey("o_key");
        $list->setOrder("asc");
        $list->setObjectTypes(array(AbstractObject::OBJECT_TYPE_VARIANT));

        return $list->load();

    }


}