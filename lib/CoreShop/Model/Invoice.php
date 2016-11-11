<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Helper\Zend\Action;
use CoreShop\Tool\Wkhtmltopdf;
use Pimcore\Logger;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Service;
use Pimcore\Tool;

/**
 * Class Invoice
 * @package CoreShop\Model
 */
class Invoice
{
    /**
     * Generates an invoice from an order.
     *
     * @param Order $order
     *
     * @throws Exception
     *
     * @return Document|bool
     */
    public static function generateInvoice(Order $order)
    {
        $locale = new \Zend_Locale($order->getLang());

        $params = [
            "order" => $order,
            "language" => (string) $locale
        ];

        $forward = new Action();
        $html = $forward->action("invoice", "invoice", "CoreShop", $params);
        $header = $forward->action("header", "invoice", "CoreShop", $params);
        $footer = $forward->action("footer", "invoice", "CoreShop", $params);

        try {
            $pdfContent = Wkhtmltopdf::fromString($html, $header, $footer, array('options' => array(Configuration::get('SYSTEM.INVOICE.WKHTML'))));

            if ($pdfContent) {
                $fileName = 'order-'.$order->getId().'.pdf';
                $path = $order->getPath();

                $invoice = Document::getByPath($path.'/'.$fileName);

                if ($invoice instanceof Document) {
                    $invoice->delete();
                }

                $invoice = new Document();
                $invoice->setFilename($fileName);
                $invoice->setParent(Service::createFolderByPath($path));
                $invoice->setData($pdfContent);
                $invoice->setProperty('order', 'object', $order);
                $invoice->save();

                $order->setProperty('invoice', 'asset', $invoice);
                $order->save();

                return $invoice;
            }
        } catch (Exception $ex) {
            Logger::warn('wkhtmltopdf library not found, no invoice was generated');
        }

        return false;
    }

    /**
     * Init Translation for view generation.
     *
     * @return null|\Pimcore\Translate|\Pimcore\Translate\Website
     *
     * @throws \Zend_Exception
     */
    protected static function initTranslation()
    {
        $translate = null;
        if (\Zend_Registry::isRegistered('Zend_Translate')) {
            $t = \Zend_Registry::get('Zend_Translate');
            // this check is necessary for the case that a document is rendered within an admin request
            // example: send test newsletter
            if ($t instanceof \Pimcore\Translate) {
                $translate = $t;
            }
        }

        if (!$translate) {
            // setup \Zend_Translate
            try {
                $locale = \CoreShop::getTools()->getLocale();

                $translate = new \Pimcore\Translate\Website($locale);

                if (Tool::isValidLanguage($locale)) {
                    $translate->setLocale($locale);
                } else {
                    Logger::error('You want to use an invalid language which is not defined in the system settings: '.$locale);
                    // fall back to the first (default) language defined
                    $languages = Tool::getValidLanguages();
                    if ($languages[0]) {
                        Logger::error("Using '".$languages[0]."' as a fallback, because the language '".$locale."' is not defined in system settings");
                        $translate = new \Pimcore\Translate\Website($languages[0]); // reinit with new locale
                        $translate->setLocale($languages[0]);
                    } else {
                        throw new Exception('You have not defined a language in the system settings (Website -> Frontend-Languages), please add at least one language.');
                    }
                }

                // register the translator in \Zend_Registry with the key "\Zend_Translate" to use the translate helper for \Zend_View
                \Zend_Registry::set('Zend_Translate', $translate);
            } catch (\Exception $e) {
                Logger::error('initialization of Pimcore_Translate failed');
                Logger::error($e);
            }
        }

        return $translate;
    }
}
