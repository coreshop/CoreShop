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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ManufacturerContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private RepositoryInterface $manufacturerRepository)
    {
    }

    /**
     * @Transform /^manufacturer "([^"]+)"$/
     */
    public function getManufacturerByName($name): ManufacturerInterface
    {
        /**
         * @var ManufacturerInterface[] $manufacturers
         */
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
