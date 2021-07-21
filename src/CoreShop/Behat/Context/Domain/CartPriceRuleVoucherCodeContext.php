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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class CartPriceRuleVoucherCodeContext implements Context
{
    private SharedStorageInterface $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then /^the generation of the codes failed$/
     */
    public function theGenerationOfTheCodesFailed()
    {
        Assert::false($this->sharedStorage->get('code-generation-possible'));
    }

    /**
     * @Then /^the generation of the codes succeeded/
     */
    public function theGenerationOfTheCodesSucceeded()
    {
        Assert::true($this->sharedStorage->get('code-generation-possible'));
    }
}
