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
use CoreShop\Component\Resource\Repository\RepositoryInterface;

final class IndexContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $indexRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface    $indexRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $indexRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->indexRepository = $indexRepository;
    }

    /**
     * @Transform /^index$/
     */
    public function index()
    {
        return $this->sharedStorage->get('index');
    }
}
