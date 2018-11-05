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
use CoreShop\Bundle\IndexBundle\Condition\MysqlRenderer;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\RendererInterface;
use Webmozart\Assert\Assert;

final class IndexConditionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then /^the (condition) rendered for "([^"]+)" should look like "([^"]+)"$/
     */
    public function theConditionForTypeShouldLookLike(ConditionInterface $condition, $rendererType, $expected)
    {
        $renderer = $this->getRenderer($rendererType);
        $is = $renderer->render($condition);

        Assert::eq(
            $is,
            $expected,
            sprintf('Expected condition to look like %s but got %s instead".', $expected, $is)
        );
    }

    /**
     * @param $type
     * @return RendererInterface
     */
    private function getRenderer($type)
    {
        switch ($type) {
            case 'mysql':
                return new MysqlRenderer();
        }

        throw new \InvalidArgumentException(sprintf('No renderer for type %s found', $type));
    }
}
