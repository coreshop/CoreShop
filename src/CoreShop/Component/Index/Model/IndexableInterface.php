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

interface IndexableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getClassId();

    /**
     * @return int
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getParent();

    public function getIndexableEnabled(IndexInterface $index): bool;
    public function getIndexable(IndexInterface $index): bool;
    public function getIndexableName(IndexInterface $index, string $language): string;
}
