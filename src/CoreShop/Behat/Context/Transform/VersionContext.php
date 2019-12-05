<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Webmozart\Assert\Assert;

final class VersionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface     $sharedStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^version/
     */
    public function version()
    {
        return $this->sharedStorage->get('product-version');
    }
}
