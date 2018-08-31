<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Payment\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface PaymentProviderTranslationInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @deprecated getName is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use getTitle instead
     *
     * @return string
     */
    public function getName();

    /**
     * @deprecated setName is deprecated since 2.0.0-beta.2 and will be removed in 2.0.0, use setTitle instead
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @param string $instructions
     */
    public function setInstructions($instructions);
}
