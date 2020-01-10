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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface OrderDocumentInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return string
     */
    public static function getDocumentType();

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     */
    public function setOrder($order);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return \DateTime
     */
    public function getDocumentDate();

    /**
     * @param \DateTime $documentDate
     */
    public function setDocumentDate($documentDate);

    /**
     * @return string
     */
    public function getDocumentNumber();

    /**
     * @param string $documentNumber
     */
    public function setDocumentNumber($documentNumber);

    /**
     * @return OrderDocumentItemInterface[]
     */
    public function getItems();

    /**
     * @param OrderDocumentItemInterface[] $items
     *
     * @return mixed
     */
    public function setItems($items);

    /**
     * @return mixed
     */
    public function getRenderedAsset();

    /**
     * @param mixed $renderedAsset
     */
    public function setRenderedAsset($renderedAsset);
}
