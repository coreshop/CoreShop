<?php

namespace CoreShop;

use CoreShop\Model\Configuration;
use Pimcore\Mail as PimcoreMail;

class Mail extends PimcoreMail {
    /**
     * Sends this email using the given transport or with the settings from "Settings" -> "System" -> "Email Settings"
     *
     * IMPORTANT: If the debug mode is enabled in "Settings" -> "System" -> "Debug" all emails will be sent to the
     * debug email addresses that are given in "Settings" -> "System" -> "Email Settings" -> "Debug email addresses"
     *
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param  \Zend_Mail_Transport_Abstract $transport
     * @return \Pimcore\Mail Provides fluent interface
     */
    public function send($transport = null)
    {
        $sendBccToUser = Configuration::get('SYSTEM.MAIL.ORDER.BCC');
        $adminMailAddress = Configuration::get('SYSTEM.MAIL.ORDER.NOTIFICATION');

        if ($sendBccToUser === true && !empty($adminMailAddress)) {
            $this->addBcc(explode(',', $adminMailAddress));
        }

        return parent::send($transport);
    }
}