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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class VariantContext implements Context
{
    public function __construct(protected SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Then /^the (attribute group "[^"]+") should have (\d+) attributes$/
     */
    public function theAttributeGroupShouldHaveXAttributes(AttributeGroupInterface $attributeGroup, int $count): void
    {
        Assert::eq(
            count($attributeGroup->getChildren()),
            $count,
            sprintf(
                '%d attributes have been found in group "%s".',
                count($attributeGroup->getChildren()),
                $attributeGroup->getRealFullPath()
            )
        );
    }

    /**
     * @Then /^the (product "[^"]+") should have (\d+) variants$/
     * @Then /^the (product) should have (\d+) variants$/
     */
    public function theProductShouldHaveVariants(ProductInterface $product, int $count): void
    {
        Assert::eq(count($product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT], true)), $count);
    }

}
