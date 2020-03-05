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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class IndexContext implements Context
{
    private $sharedStorage;
    private $indexRepository;

    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $indexRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->indexRepository = $indexRepository;
    }

    /**
     * @Transform /^index "([^"]+)"$/
     */
    public function getIndexByName($name)
    {
        $indexes = $this->indexRepository->findBy(['name' => $name]);

        Assert::eq(
            count($indexes),
            1,
            sprintf('%d indices have been found with name "%s".', count($indexes), $name)
        );

        return reset($indexes);
    }

    /**
     * @Transform /^index$/
     */
    public function index()
    {
        return $this->sharedStorage->get('index');
    }
}
