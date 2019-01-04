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
use CoreShop\Component\Resource\Repository\RepositoryInterface;

final class FilterContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $filterRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface    $filterRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $filterRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->filterRepository = $filterRepository;
    }

    /**
     * @Transform /^filter$/
     */
    public function filter()
    {
        return $this->sharedStorage->get('filter');
    }
}
