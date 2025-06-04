<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ZoneContext implements Context
{
    public function __construct(
        private RepositoryInterface $zoneRepository,
    ) {
    }

    /**
     * @Transform /^zone(?:|s) "([^"]+)"$/
     */
    public function getZoneByName(string $name): ZoneInterface
    {
        /**
         * @var ZoneInterface[] $zones
         */
        $zones = $this->zoneRepository->findBy(['name' => $name]);

        Assert::eq(
            count($zones),
            1,
            sprintf('%d country has been found with name "%s".', count($zones), $name),
        );

        return reset($zones);
    }
}
