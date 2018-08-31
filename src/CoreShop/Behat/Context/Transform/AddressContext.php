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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

final class AddressContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $addressRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PimcoreRepositoryInterface $addressRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PimcoreRepositoryInterface $addressRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @Transform /^address$/
     */
    public function address()
    {
        return $this->sharedStorage->get('address');
    }
}
