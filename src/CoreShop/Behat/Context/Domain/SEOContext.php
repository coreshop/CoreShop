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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;
use Webmozart\Assert\Assert;

final class SEOContext implements Context
{
    public function __construct(
        private SEOPresentationInterface $seoPresentation,
        private HeadTitle $headTitle,
        private HeadMeta $headMeta,
    ) {
    }

    /**
     * @Then /^the (product "[^"]+") should have meta title "([^"]+)"$/
     * @Then /^the (product) should have meta title "([^"]+)"$/
     */
    public function productShouldHaveMetaTitle(ProductInterface $product, string $title): void
    {
        $this->seoPresentation->updateSeoMetadata($product);

        Assert::same($product->getMetaTitle(), $title);
        Assert::same($this->headTitle->toString(), sprintf('<title>%s</title>', $title));
    }

    /**
     * @Then /^the (product "[^"]+") should have meta description "([^"]+)"$/
     * @Then /^the (product) should have meta description "([^"]+)"$/
     */
    public function productShouldHaveMetaDescription(ProductInterface $product, string $description): void
    {
        $this->seoPresentation->updateSeoMetadata($product);

        $descriptionItem = null;

        foreach ($this->headMeta->getContainer()->getArrayCopy() as $item) {
            if (!$item instanceof \stdClass) {
                continue;
            }

            if (!isset($item->name)) {
                continue;
            }

            if ($item->name === 'description') {
                $descriptionItem = $item;

                break;
            }
        }

        Assert::same($product->getMetaDescription(), $description);
        Assert::same($this->headMeta->itemToString($descriptionItem), sprintf('<meta name="description" content="%s" />', $description));
    }
}
