<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Variant\Model\AttributeColorInterface;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeValue;
use CoreShop\Component\Variant\Model\AttributeValueInterface;
use Webmozart\Assert\Assert;

final class VariantContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $attributeGroupRepository,
        private RepositoryInterface $attributeColorRepository,
        private RepositoryInterface $attributeValueRepository,

    )
    {
    }

    /**
     * @Transform /^attribute group "([^"]+)"$/
     */
    public function getAttributeGroup($name): AttributeGroupInterface
    {
        /**
         * @var AttributeGroupInterface[] $attributeGroups
         */
        $attributeGroups = $this->attributeGroupRepository->findBy(['name' => $name]);

        Assert::eq(
            count($attributeGroups),
            1,
            sprintf('%d Attribute Group has been found with name "%s".', count($attributeGroups), $name)
        );

        return reset($attributeGroups);
    }

    /**
     * @Transform /^attribute color "([^"]+)"$/
     */
    public function getAttributeColor($name): AttributeColorInterface
    {
        /**
         * @var AttributeColorInterface[] $attributeColor
         */
        $attributes = $this->attributeColorRepository->findBy(['name' => $name]);

        Assert::eq(
            count($attributes),
            1,
            sprintf('%d Attribute Color has been found with name "%s".', count($attributes), $name)
        );

        return reset($attributes);
    }

    /**
     * @Transform /^attribute value "([^"]+)"$/
     */
    public function getAttributeValue($name): AttributeValueInterface
    {
        /**
         * @var AttributeColorInterface[] $attributeColor
         */
        $attributes = $this->attributeValueRepository->findBy(['name' => $name]);

        Assert::eq(
            count($attributes),
            1,
            sprintf('%d Attribute Value has been found with name "%s".', count($attributes), $name)
        );

        return reset($attributes);
    }

    /**
     * @Transform /^attribute group/
     */
    public function attributeGroup()
    {
        return $this->sharedStorage->get('attribute-group');
    }
}
