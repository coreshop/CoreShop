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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Symfony\Component\EventDispatcher\Event;

final class WkhtmlOptionsEvent extends Event
{
    /**
     * @var string
     */
    protected $options;

    /**
     * @var OrderDocumentInterface
     */
    protected $orderDocument;

    /**
     * @param OrderDocumentInterface $orderDocument
     */
    public function __construct(OrderDocumentInterface $orderDocument)
    {
        $this->orderDocument = $orderDocument;
    }

    /**
     * @return OrderDocumentInterface
     */
    public function getOrderDocument(): OrderDocumentInterface
    {
        return $this->orderDocument;
    }

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return $this->options;
    }

    /**
     * @param string $options
     */
    public function setOptions(string $options)
    {
        $this->options = $options;
    }
}
