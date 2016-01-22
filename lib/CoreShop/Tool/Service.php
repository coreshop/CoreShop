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

class Service
{

    private static $allowedVariationTypes = array("input","numeric","checkbox","select","slider");

    public static function getDimensionVariations( Product $product ) {

        $productVariants = $product->getChilds(array(AbstractObject::OBJECT_TYPE_VARIANT));

        $variantsAndMaster = array_merge(array($product), $productVariants);

        $productMethods = self::getProductValidMethods( $product, 'CoreShop\Model\Product' );

        //we do have some dimension entries!
        $dimensionsData = $product->getDimensions();

        $brickGetters = $dimensionsData->getBrickGetters();

        $dimensionInfo = array();

        if( !empty( $brickGetters ) ) {

            foreach( $brickGetters as $brickGetter ) {

                $getter = $dimensionsData->{$brickGetter}();

                if( is_null( $getter ) )
                    continue;

                $dimensionMethods = self::getProductValidMethods( $getter, get_class( $getter ), array('getType'), $getter->getType() );

                $dimensionInfo[] = array(

                    'getterName' => $brickGetter,
                    'methods' => $dimensionMethods

                );

            }

        }

        $validData = array(

            'productMethods' => $productMethods,
            'dimensionInfo' => $dimensionInfo

        );

        $compareValues = array();

        foreach ($variantsAndMaster as $productVariant) {

            $productId = $productVariant->getId();

            if( !empty( $validData['productMethods'])) {

                foreach( $validData['productMethods'] as $pMethod ) {

                    $getpMethod = 'get' . $pMethod;

                    $compareValues[ $pMethod ][ $productId ] = $productVariant->{$getpMethod}();

                }

            }

            if( !empty( $validData['dimensionInfo'] )) {

                foreach( $validData['dimensionInfo'] as $dData ) {

                    $getterName = $dData['getterName'];
                    $getter = $productVariant->getDimensions()->{$getterName}();

                    if( !empty( $dData['methods'] )) {

                        foreach( $dData['methods'] as $dMethod ) {

                            $getdMethod = 'get' . $dMethod;
                            $compareValues[ $getter->getType() . '_' . $dMethod ][ $productId ] = $getter->{$getdMethod}();

                        }

                    }

                }

            }

        }

        $validVariations = array();

        foreach( $compareValues as $variantName => $variantValues ) {

            if( count(array_unique( $variantValues )) !== 1 )
                $validVariations[ $variantName ] = $variantValues;

        }

        return $validVariations;

    }

    private static function getProductValidMethods( $obj, $classNameSpace = NULL, $excludeMethods = NULL, $brickName = FALSE ) {

        $exclude = array_flip(

            $excludeMethods !== NULL ? $excludeMethods : array(

                'getById',
                'getAll',
                'getLatest',
                'getImage',
                'getName',
                'getImages',
                'getDefaultImage',
                'getCategories',
                'getIsNew',
                'getVariantDifferences',
                'getPrice',
                'getRetailPrice',
                'getTax','getTaxRule',
                'getTaxCalculator',
                'getPriceWithoutTax',
                'getSpecificPrice',
                'getWholesalePrice',
                'getIsDownloadProduct'

            )
        );

        $fields = array_map( function($v) { return substr($v->name, 3); },

            array_filter( (new \ReflectionClass($classNameSpace) )->getMethods(\ReflectionMethod::IS_PUBLIC),

                function ($v) use ($classNameSpace, $exclude) {

                    return (strpos($v->name, 'get') === 0 && $v->class === $classNameSpace) && !array_key_exists($v->name, $exclude);

                }

            )

        );

        if( empty( $fields ) )
            return array();

        $valid = array();

        foreach( $fields as $method ) {

            if( $brickName !== FALSE ) {

                $fieldDefinition = Definition::getByKey( $brickName )->getFieldDefinition( strtolower($method) );

            } else {

                $fieldDefinition = $obj->getClass()->getFieldDefinition( strtolower($method) );
            }

            if( $fieldDefinition !== FALSE && in_array( $fieldDefinition->getFieldType(), self::$allowedVariationTypes ) )
                $valid[] = $method;

        }

        return $valid;

    }

}