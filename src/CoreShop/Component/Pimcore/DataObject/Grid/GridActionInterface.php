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

namespace CoreShop\Component\Pimcore\DataObject\Grid;

interface GridActionInterface
{
    /**
     * The name of action.
     * This value will be translated via backend translator,
     * so it's good practice to choose a symfony standard translation keys like "coreshop.grid.action.your_action_name".
     *
     * @return string
     */
    public function getName();

    /**
     * @param array $processIds
     *
     * @return string
     */
    public function apply(array $processIds);

    /**
     * Define if action is valid for $type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function supports($type);
}
