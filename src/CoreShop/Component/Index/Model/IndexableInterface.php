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

namespace CoreShop\Component\Index\Model;

interface IndexableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function getIndexableEnabled();

    /**
     * @return bool
     */
    public function getIndexable();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return int
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
     * @param string $language
     *
     * @return string
     */
    public function getIndexableName($language);

    /**
     * @return mixed
     */
    public function getParent();
}
