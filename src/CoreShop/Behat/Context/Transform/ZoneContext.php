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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ZoneContext implements Context
{
    private $zoneRepository;

    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * @Transform /^zone(?:|s) "([^"]+)"$/
     */
    public function getZoneByName($name)
    {
        $zones = $this->zoneRepository->findBy(['name' => $name]);

        Assert::eq(
            count($zones),
            1,
            sprintf('%d country has been found with name "%s".', count($zones), $name)
        );

        return reset($zones);
    }
}
