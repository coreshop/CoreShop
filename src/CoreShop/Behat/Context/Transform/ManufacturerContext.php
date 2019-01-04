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
use Webmozart\Assert\Assert;

final class ManufacturerContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $manufacturerRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface    $manufacturerRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $manufacturerRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @Transform /^manufacturer "([^"]+)"$/
     */
    public function getManufacturerByName($name)
    {
        $manufacturers = $this->manufacturerRepository->findBy(['name' => $name]);

        Assert::eq(
            count($manufacturers),
            1,
            sprintf('%d Manufacturers has been found with name "%s".', count($manufacturers), $name)
        );

        return reset($manufacturers);
    }

    /**
     * @Transform /^manufacturer/
     */
    public function manufacturer()
    {
        return $this->sharedStorage->get('manufacturer');
    }
}
