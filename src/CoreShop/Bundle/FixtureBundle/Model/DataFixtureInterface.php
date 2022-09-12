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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
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
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getVersion();
}
