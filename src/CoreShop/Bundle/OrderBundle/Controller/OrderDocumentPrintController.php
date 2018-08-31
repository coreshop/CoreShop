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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;

class OrderDocumentPrintController extends FrontendController
{
    public function invoiceAction(Request $request, OrderDocumentInterface $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:invoice.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType()
        ]);
    }

    public function shipmentAction(Request $request, OrderDocumentInterface $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:shipment.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType()
        ]);
    }

    public function headerAction(Request $request, OrderDocumentInterface $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:header.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType()
        ]);
    }

    public function footerAction(Request $request, OrderDocumentInterface $document, $order)
    {
        return $this->render('CoreShopOrderBundle:OrderDocumentPrint:footer.html.twig', [
            'document' => $document,
            'order' => $order,
            'type' => $document::getDocumentType()
        ]);
    }
}
