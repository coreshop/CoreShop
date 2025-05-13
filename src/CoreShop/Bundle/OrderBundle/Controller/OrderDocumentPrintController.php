<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderDocumentPrintController extends FrontendController
{
    public function invoiceAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/invoice.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function shipmentAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/shipment.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function headerAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/header.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }

    public function footerAction(Request $request, OrderDocumentInterface $document, OrderInterface $order): Response
    {
        return $this->render('@CoreShopOrder/OrderDocumentPrint/footer.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType(),
        ]);
    }
}
