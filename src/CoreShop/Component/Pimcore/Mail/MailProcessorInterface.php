<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Mail;

use Pimcore\Model\Document\Email;

interface MailProcessorInterface
{
    /**
     * @param Email $emailDocument
     * @param null  $subject
     * @param mixed $recipients
     * @param array $attachments
     * @param array $params
     *
     * @return bool
     */
    public function sendMail(Email $emailDocument, $subject = null, $recipients = null, $attachments = [], $params = []);
}

class_alias(MailProcessorInterface::class, 'CoreShop\Bundle\PimcoreBundle\Mail\MailProcessorInterface');
