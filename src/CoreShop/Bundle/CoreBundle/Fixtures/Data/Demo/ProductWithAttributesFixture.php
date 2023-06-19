<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Resource\Model\AbstractObject;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Barcode;
use Faker\Provider\Lorem;

class ProductWithAttributesFixture extends AbstractProductFixture implements FixtureGroupInterface
{
    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [
            AttributeGroupsFixture::class,
        ]);
    }

    public static function getGroups(): array
    {
        return ['demo'];
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
                    'size' => 's',
                ],
                [
                    'size' => 'm',
                ],
                [
                    'size' => 'l',
                ],
                [
                    'size' => 'xl',
                ],
            ],
            [
                [
                    'color' => 'red',
                    'size' => 's',
                ],
                [
                    'color' => 'red',
                    'size' => 'm',
                ],
                [
                    'color' => 'red',
                    'size' => 'l',
                ],
                [
                    'color' => 'red',
                    'size' => 'xl',
                ],
                [
                    'color' => 'black',
                    'size' => 's',
                ],
                [
                    'color' => 'black',
                    'size' => 'm',
                ],
                [
                    'color' => 'black',
                    'size' => 'l',
                ],
                [
                    'color' => 'black',
                    'size' => 'xl',
                ],
                [
                    'color' => 'blue',
                    'size' => 's',
                ],
                [
                    'color' => 'blue',
                    'size' => 'm',
                ],
                [
                    'color' => 'blue',
                    'size' => 'l',
                ],
                [
                    'color' => 'blue',
                    'size' => 'xl',
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
            ],
        ];

        for ($i = 0; $i < $productsCount; ++$i) {
            $product = $this->createProduct('products-with-attributes');

            $variantType = $possibleVariants[array_rand($possibleVariants)];

            $usedAttributeGroups = array_keys($variantType[0]);
            $allowedAttributeGroups = [];

            foreach ($usedAttributeGroups as $attributeGroupKey) {
                $allowedAttributeGroups[] = $this->attributeGroupRepository->findOneBy(['name' => $attributeGroupKey]);
            }

            $product->setAllowedAttributeGroups($allowedAttributeGroups);
            $product->save();

            for ($x = 0; $x < $variants; ++$x) {
                if (count($variantType) === 0) {
                    break;
                }

                $variantAttributesKey = array_rand($variantType);
                $variantAttributes = $variantType[$variantAttributesKey];

                unset($variantType[$variantAttributesKey]);

                $attributes = array_map(function ($key, $value) {
                    if ($key === 'color') {
                        return $this->attributeColorRepository->findOneBy(['name' => $value]);
                    }

                    return $this->attributeValueRepository->findOneBy(['name' => $value]);
                }, array_keys($variantAttributes), $variantAttributes);

                $variant = $this->productFactory->createNew();
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
