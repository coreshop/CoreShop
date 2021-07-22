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

namespace CoreShop\Component\Index\Service;

interface IndexUpdaterServiceInterface
{
    /**
     * Update all Indices with $subject.
     *
     * @param mixed $subject
     * @param bool $isVersionChange
     */
    public function updateIndices($subject, bool $isVersionChange = false);

    /**
     * Remove all Indices with $subject.
     *
     * @param mixed $subject
     */
    public function removeIndices($subject);
}
