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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderDocumentInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return string
     */
    public static function getDocumentType(): string;

    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order);

    public function getState(): ?string;

    public function setState(?string $state);

    public function getDocumentDate(): ?Carbon;

    public function setDocumentDate(?Carbon $documentDate);

    public function getDocumentNumber(): ?string;

    public function setDocumentNumber(?string $documentNumber);

    /**
     * @return OrderDocumentItemInterface[]
     */
    public function getItems(): ?array;

    /**
     * @param OrderDocumentItemInterface[] $items
     */
    public function setItems(array $items);

    /**
     * @return mixed
     */
    public function getRenderedAsset();

    /**
     * @param mixed $renderedAsset
     */
    public function setRenderedAsset($renderedAsset);
}
