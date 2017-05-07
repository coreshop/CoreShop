<?php

namespace CoreShop\Bundle\OrderBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;

class OrderDocumentPrintController extends FrontendController
{
    public function invoiceAction(Request $request, $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:invoice.html.twig', [
            'document' => $document,
            'order' => $order
        ]);
    }

    public function shipmentAction(Request $request, $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:shipment.html.twig', [
            'document' => $document,
            'order' => $order
        ]);
    }

    public function headerAction(Request $request, $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:header.html.twig', [
            'document' => $document,
            'order' => $order
        ]);
    }

    public function footerAction(Request $request, $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:footer.html.twig', [
            'document' => $document,
            'order' => $order
        ]);
    }
}