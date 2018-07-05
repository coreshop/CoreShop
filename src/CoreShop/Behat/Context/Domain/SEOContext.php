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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\SEO\SEOPresentationInterface;
use Pimcore\Templating\Helper\HeadMeta;
use Pimcore\Templating\Helper\HeadTitle;
use Webmozart\Assert\Assert;

final class SEOContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SEOPresentationInterface
     */
    private $seoPresentation;

    /**
     * @var HeadTitle
     */
    private $headTitle;

    /**
     * @var HeadMeta
     */
    private $headMeta;

    /**
     * @param SharedStorageInterface   $sharedStorage
     * @param SEOPresentationInterface $seoPresentation
     * @param HeadTitle                $headTitle
     * @param HeadMeta                 $headMeta
     */
    public function __construct(SharedStorageInterface $sharedStorage, SEOPresentationInterface $seoPresentation, HeadTitle $headTitle, HeadMeta $headMeta)
    {
        $this->sharedStorage = $sharedStorage;
        $this->seoPresentation = $seoPresentation;
        $this->headTitle = $headTitle;
        $this->headMeta = $headMeta;
    }

    /**
     * @Then /^the (product "[^"]+") should have meta title "([^"]+)"$/
     * @Then /^the (product) should have meta title "([^"]+)"$/
     */
    public function productShouldHaveMetaTitle(ProductInterface $product, $title)
    {
        $this->seoPresentation->updateSeoMetadata($product);

        Assert::same($product->getMetaTitle(), $title);
        Assert::same($this->headTitle->toString(), sprintf('<title>%s</title>', $title));
    }

    /**
     * @Then /^the (product "[^"]+") should have meta description "([^"]+)"$/
     * @Then /^the (product) should have meta description "([^"]+)"$/
     */
    public function productShouldHaveMetaDescription(ProductInterface $product, $description)
    {
        $this->seoPresentation->updateSeoMetadata($product);

        $descriptionItem = null;

        foreach ($this->headMeta as $item) {
            if ('description' === $item->name) {
                $descriptionItem = $item;

                break;
            }
        }

        Assert::same($product->getMetaDescription(), $description);
        Assert::same($this->headMeta->itemToString($descriptionItem), sprintf('<meta name="description" content="%s" />', $description));
    }
}
