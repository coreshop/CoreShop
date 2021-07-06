<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\HomePageInterface;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    private $homePage;

    public function __construct(HomePageInterface $homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @When I check latest products
     * @When I visit the homepage
     */
    public function iCheckLatestProducts(): void
    {
        $this->homePage->open();
    }

    /**
     * @Then I should be redirected to the homepage
     */
    public function iShouldBeRedirectedToTheHomepage(): void
    {
        $this->homePage->verify();
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList(int $numberOfProducts): void
    {
        Assert::same(count($this->homePage->getLatestProductsNames()), $numberOfProducts);
    }
}
