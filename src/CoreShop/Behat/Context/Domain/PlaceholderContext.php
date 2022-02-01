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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class PlaceholderContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then /^the placeholder value for expression "([^"]+)" should be "([^"]+)"$/
     */
    public function placeHolderValueShouldBe($expression, $value)
    {
        $placeholderHelper = new \Pimcore\Placeholder();
        $data = [];
        $executedValue = $placeholderHelper->replacePlaceholders($expression, $data);

        Assert::same(
            $executedValue,
            $value,
            sprintf('Expression value should be "%s" but is %s instead', $executedValue, $value)
        );
    }

    /**
     * @Then /^the placeholder value for expression "([^"]+)" for (object) should be "([^"]+)"$/
     */
    public function placeHolderValueForObjectShouldBe($expression, $object, $value)
    {
        $placeholderHelper = new \Pimcore\Placeholder();
        $data = [
            'object' => $object,
        ];
        $executedValue = $placeholderHelper->replacePlaceholders($expression, $data);

        Assert::same(
            $executedValue,
            $value,
            sprintf('Expression value should be "%s" but is %s instead', $executedValue, $value)
        );
    }
}
