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

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\SetValuesTrait;

class DataFixture extends AbstractResource implements DataFixtureInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var \DateTime
     */
    protected $loadedAt;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadedAt()
    {
        return $this->loadedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoadedAt($loadedAt)
    {
        $this->loadedAt = $loadedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }
}
