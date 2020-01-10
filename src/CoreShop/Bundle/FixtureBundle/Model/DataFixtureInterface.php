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

namespace CoreShop\Bundle\FixtureBundle\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface DataFixtureInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getClassName();

    /**
     * @param string $className
     */
    public function setClassName($className);

    /**
     * @return \DateTime
     */
    public function getLoadedAt();

    /**
     * @param \DateTime $loadedAt
     */
    public function setLoadedAt($loadedAt);

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getVersion();
}
