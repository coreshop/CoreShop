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

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

interface PriceRuleInterface extends RuleInterface, TranslatableInterface
{
    /**
     * @return string
     */
    public function getLabel(?string $language = null);

    public function setLabel(string $label, ?string $language = null);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return bool
     */
    public function getStopPropagation();

    /**
     * @param bool $stopPropagation
     */
    public function setStopPropagation($stopPropagation);
}
