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

namespace CoreShop\Component\Order\Checkout;

class CheckoutException extends \RuntimeException
{
    /**
     * @var string
     */
    private $translatableText;

    /**
     * {@inheritdoc}
     */
    public function __construct($reason, $translatableText)
    {
        parent::__construct($reason);

        $this->translatableText = $translatableText;
    }

    /**
     * @return string
     */
    public function getTranslatableText()
    {
        return $this->translatableText;
    }

    /**
     * @param string $translatableText
     */
    public function setTranslatableText($translatableText)
    {
        $this->translatableText = $translatableText;
    }
}
