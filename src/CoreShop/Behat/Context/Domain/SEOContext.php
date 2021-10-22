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
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;
use Webmozart\Assert\Assert;

final class SEOContext implements Context
{
    public function __construct(private SEOPresentationInterface $seoPresentation, private HeadTitle $headTitle, private HeadMeta $headMeta)
    {
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

            if ('description' === $item->name) {
                $descriptionItem = $item;

                break;
            }
        }

        Assert::same($product->getMetaDescription(), $description);
        Assert::same($this->headMeta->itemToString($descriptionItem), sprintf('<meta name="description" content="%s" />', $description));
    }
}
