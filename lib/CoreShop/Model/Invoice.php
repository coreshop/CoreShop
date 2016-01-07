<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Tool\Wkhtmltopdf;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Service;

class Invoice
{
    /**
     * Generates an invoice from an order
     *
     * @param Order $order
     * @throws Exception
     *
     * @return Document|boolean
     */
    public static function generateInvoice(Order $order)
    {
        $view = new \Pimcore\View();
        $view->setScriptPath(CORESHOP_TEMPLATE_PATH . "/views/scripts/coreshop/invoice/");
        $view->assign("order", $order);
        $view->addHelperPath(PIMCORE_PATH . "/lib/Pimcore/View/Helper", "\\Pimcore\\View\\Helper\\");
        $view->addHelperPath(CORESHOP_PATH . '/lib/CoreShop/View/Helper', 'CoreShop\View\Helper');
        $html = $view->render("invoice.php");
        $header = $view->render("header.php");
        $footer = $view->render("footer.php");

        try {
            $pdfContent = Wkhtmltopdf::fromString($html, $header, $footer, array("options" => array("-T" => "35mm")));

            if ($pdfContent) {
                $fileName = "order-" . $order->getId() . ".pdf";
                $path = $order->getPath();

                $invoice = Document::getByPath($path . "/" . $fileName);

                if($invoice instanceof Document) {
                    $invoice->delete();
                }

                $invoice = new Document();
                $invoice->setFilename($fileName);
                $invoice->setParent(Service::createFolderByPath($path));
                $invoice->setData($pdfContent);
                $invoice->setProperty("order", "object", $order);
                $invoice->save();

                $order->setProperty("invoice", "asset", $invoice);
                $order->save();

                return $invoice;
            }
        }
        catch(Exception $ex) {
            \Logger::warn("wkhtmltopdf library not found, no invoice was generated");
        }

        return false;
    }
}