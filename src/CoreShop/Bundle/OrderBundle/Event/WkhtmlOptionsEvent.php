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

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class WkhtmlOptionsEvent extends Event
{
    protected OrderDocumentInterface $orderDocument;
    protected string $options;

    public function __construct(OrderDocumentInterface $orderDocument)
    {
        $this->orderDocument = $orderDocument;
    }

    public function getOrderDocument(): OrderDocumentInterface
    {
        return $this->orderDocument;
    }

    public function getOptions(): string
    {
        return $this->options;
    }

    public function setOptions(string $options): void
    {
        $this->options = $options;
    }
}
