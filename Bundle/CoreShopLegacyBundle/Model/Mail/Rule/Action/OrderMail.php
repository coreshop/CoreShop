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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Action;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Document;

/**
 * Class OrderMail
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Mail\Rule\Action
 */
class OrderMail extends Mail
{
    /**
     * @var string
     */
    public static $type = 'orderMail';

    /**
     * @var boolean
     */
    public $sendInvoices;

    /**
     * @var boolean
     */
    public $sendShipments;

    /**
     * @var boolean
     */
    public $sendBcc;

    /**
     * @param AbstractModel $model
     * @param Model\Mail\Rule $rule
     * @param array $params
     *
     * @throws Exception
     */
    public function apply(AbstractModel $model, Model\Mail\Rule $rule, $params = [])
    {
        $order = null;

        if ($model instanceof Model\Order\Invoice) {
            $order = $model->getOrder();
        } elseif ($model instanceof Model\Order\Shipment) {
            $order = $model->getOrder();
        } elseif ($model instanceof Model\Order) {
            $order = $model;
        }

        if ($order instanceof Model\Order) {
            $language = $model->getLang();

            if (!$language && \Zend_Registry::isRegistered('Zend_Locale')) {
                $language = (string)\Zend_Registry::get('Zend_Locale');
            }

            if (is_null($language)) {
                throw new Exception('Language is not set');
            }

            if (array_key_exists($language, $this->getMails())) {
                $mailDocumentId = $this->mails[$language];
                $mailDocument = Document::getById($mailDocumentId);

                $params['mailRule'] = $rule;
                $params['document'] = $model;

                if ($mailDocument instanceof Document\Email) {
                    \CoreShop\Bundle\CoreShopLegacyBundle\Mail::sendOrderMail($mailDocument, $order, $this->getSendInvoices(), $this->getSendShipments(), $params);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function getSendInvoices()
    {
        return $this->sendInvoices;
    }

    /**
     * @param bool $sendInvoices
     */
    public function setSendInvoices($sendInvoices)
    {
        $this->sendInvoices = $sendInvoices;
    }

    /**
     * @return bool
     */
    public function getSendShipments()
    {
        return $this->sendShipments;
    }

    /**
     * @param bool $sendShipments
     */
    public function setSendShipments($sendShipments)
    {
        $this->sendShipments = $sendShipments;
    }
}
