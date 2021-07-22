<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface IndexInterface extends ResourceInterface, TimestampableInterface
{
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

    /**
     * @param IndexColumnInterface $column
     */
    public function addColumn(IndexColumnInterface $column);

    /**
     * @param IndexColumnInterface $column
     */
    public function removeColumn(IndexColumnInterface $column);

    /**
     * @param IndexColumnInterface $column
     *
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
