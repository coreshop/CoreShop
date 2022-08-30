<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Model\ProductUnitDefinitionPriceInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Barcode;
use Faker\Provider\Lorem;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Tool;

class ProductWithAttributesFixture extends AbstractProductFixture
{
    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [
            AttributeGroupsFixture::class
        ]);
    }

    public function load(ObjectManager $manager): void
    {
        $productsCount = 5;
        $variants = 6;
        $faker = Factory::create();
        $faker->addProvider(new Lorem($faker));
        $faker->addProvider(new Barcode($faker));

        $possibleVariants = [
            [
                [
                    'size' => 's'
                ],
                [
                    'size' => 'm'
                ],
                [
                    'size' => 'l'
                ],
                [
                    'size' => 'xl'
                ],
            ],
            [
                [
                    'color' => 'red',
                    'size' => 's'
                ],
                [
                    'color' => 'red',
                    'size' => 'm'
                ],
                [
                    'color' => 'red',
                    'size' => 'l'
                ],
                [
                    'color' => 'red',
                    'size' => 'xl'
                ],
                [
                    'color' => 'black',
                    'size' => 's'
                ],
                [
                    'color' => 'black',
                    'size' => 'm'
                ],
                [
                    'color' => 'black',
                    'size' => 'l'
                ],
                [
                    'color' => 'black',
                    'size' => 'xl'
                ],
                [
                    'color' => 'blue',
                    'size' => 's'
                ],
                [
                    'color' => 'blue',
                    'size' => 'm'
                ],
                [
                    'color' => 'blue',
                    'size' => 'l'
                ],
                [
                    'color' => 'blue',
                    'size' => 'xl'
                ],
            ],
            [
                [
                    'color' => 'red',
                    'size' => 's',
                    'season' => 'winter',
                ],
                [
                    'color' => 'red',
                    'size' => 'm',
                    'season' => 'winter',
                ],
                [
                    'color' => 'red',
                    'size' => 'l',
                    'season' => 'winter',
                ],
                [
                    'color' => 'red',
                    'size' => 'xl',
                    'season' => 'winter',
                ],
                [
                    'color' => 'black',
                    'size' => 's',
                    'season' => 'winter',
                ],
                [
                    'color' => 'black',
                    'size' => 'm',
                    'season' => 'winter',
                ],
                [
                    'color' => 'black',
                    'size' => 'l',
                    'season' => 'winter',
                ],
                [
                    'color' => 'black',
                    'size' => 'xl',
                    'season' => 'winter',
                ],
                [
                    'color' => 'blue',
                    'size' => 's',
                    'season' => 'winter',
                ],
                [
                    'color' => 'blue',
                    'size' => 'm',
                    'season' => 'winter',
                ],
                [
                    'color' => 'blue',
                    'size' => 'l',
                    'season' => 'winter',
                ],
                [
                    'color' => 'blue',
                    'size' => 'xl',
                    'season' => 'winter',
                ],
                [
                    'color' => 'red',
                    'size' => 's',
                    'season' => 'summer',
                ],
                [
                    'color' => 'red',
                    'size' => 'm',
                    'season' => 'summer',
                ],
                [
                    'color' => 'red',
                    'size' => 'l',
                    'season' => 'summer',
                ],
                [
                    'color' => 'red',
                    'size' => 'xl',
                    'season' => 'summer',
                ],
                [
                    'color' => 'black',
                    'size' => 's',
                    'season' => 'summer',
                ],
                [
                    'color' => 'black',
                    'size' => 'm',
                    'season' => 'summer',
                ],
                [
                    'color' => 'black',
                    'size' => 'l',
                    'season' => 'summer',
                ],
                [
                    'color' => 'black',
                    'size' => 'xl',
                    'season' => 'summer',
                ],
                [
                    'color' => 'blue',
                    'size' => 's',
                    'season' => 'summer',
                ],
                [
                    'color' => 'blue',
                    'size' => 'm',
                    'season' => 'summer',
                ],
                [
                    'color' => 'blue',
                    'size' => 'l',
                    'season' => 'summer',
                ],
                [
                    'color' => 'blue',
                    'size' => 'xl',
                    'season' => 'summer',
                ],
            ]
        ];

        for ($i = 0; $i < $productsCount; ++$i) {
            $product = $this->createProduct('products-with-attributes');

            $variantType = $possibleVariants[array_rand($possibleVariants)];

            $usedAttributeGroups = array_keys($variantType[0]);
            $allowedAttributeGroups = [];

            foreach ($usedAttributeGroups as $attributeGroupKey) {
                $allowedAttributeGroups[] = $this->container->get('coreshop.repository.attribute_group')->findOneBy(['name' => $attributeGroupKey]);
            }

            $product->setAllowedAttributeGroups($allowedAttributeGroups);
            $product->save();

            for ($x = 0; $x < $variants; $x++) {
                if (count($variantType) === 0) {
                    break;
                }
                
                $variantAttributesKey = array_rand($variantType);
                $variantAttributes = $variantType[$variantAttributesKey];

                unset($variantType[$variantAttributesKey]);

                $attributes = array_map(function($key, $value) {
                    if ($key === 'color') {
                        return $this->container->get('coreshop.repository.attribute_color')->findOneBy(['name' => $value]);
                    }

                    return $this->container->get('coreshop.repository.attribute_value')->findOneBy(['name' => $value]);
                }, array_keys($variantAttributes), $variantAttributes);

                $variant = $this->container->get('coreshop.factory.product')->createNew();
                $variant->setKey(implode(' - ', $variantAttributes));
                $variant->setParent($product);
                $variant->setPublished(true);
                $variant->setType(AbstractObject::OBJECT_TYPE_VARIANT);
                $variant->setAttributes($attributes);
                $variant->save();
            }
        }
    }
}
