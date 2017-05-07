<?php

namespace CoreShop\Component\Order\Renderer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;

interface OrderDocumentRendererInterface
{
    /**
     * Renders a Order Document as PDF
     *
     * @param OrderDocumentInterface $orderDocument
     * @return string
     */
    public function renderDocumentPdf(OrderDocumentInterface $orderDocument);
}