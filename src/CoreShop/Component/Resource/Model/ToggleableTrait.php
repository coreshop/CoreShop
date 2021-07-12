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

declare(strict_types=1);

namespace CoreShop\Component\Resource\Model;

trait ToggleableTrait
{
    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @return bool
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $enabled
     */
    public function setActive(?bool $enabled)
    {
        $this->active = (bool) $enabled;
    }

    public function activate()
    {
        $this->active = true;
    }

    public function disable()
    {
        $this->active = false;
    }
}
