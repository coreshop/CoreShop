<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Config;
use CoreShop\Exception;
use CoreShop\Tool\Wkhtmltopdf;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Service;
use Pimcore\View;

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
        $locale = new \Zend_Locale($order->getLang());
        \Zend_Locale::setDefault($locale);
        \Zend_Registry::set("Zend_Locale", $locale);

        $view = new View();
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