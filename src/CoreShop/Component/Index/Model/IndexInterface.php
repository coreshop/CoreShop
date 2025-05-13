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

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface IndexInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getWorker();

    /**
     * @param string $worker
     */
    public function setWorker($worker);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @param string $class
     */
    public function setClass($class);

    /**
     * @return Collection|IndexColumnInterface[]
     */
    public function getColumns();

    /**
     * @return bool
     */
    public function hasColumns();

    public function addColumn(IndexColumnInterface $column);

    public function removeColumn(IndexColumnInterface $column);

    /**
     * @return bool
     */
    public function hasColumn(IndexColumnInterface $column);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration);

    /**
     * @return bool
     */
    public function getIndexLastVersion();

    /**
     * @param bool $indexLastVersion
     */
    public function setIndexLastVersion($indexLastVersion);
}
