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

namespace CoreShop\Component\Pimcore;

class Mail extends \Pimcore\Mail
{
    /**
     * @param array|string $recipients
     */
    public function addRecipients($recipients = null)
    {
        $toRecipients = [];

        if ($recipients) {
            if (is_array($recipients)) {
                foreach ($recipients as $recipient) {
                    if (is_array($recipient)) {
                        $toRecipients[] = [$recipient[0], $recipient[1]];
                    } else {
                        $multiRecipients = array_filter(explode(';', $recipient));
                        foreach ($multiRecipients as $multiRecipient) {
                            $toRecipients[] = [$multiRecipient, ''];
                        }
                    }
                }
            } else {
                $multiRecipients = array_filter(explode(';', $recipients));
                foreach ($multiRecipients as $multiRecipient) {
                    $toRecipients[] = [$multiRecipient, ''];
                }
            }
        }

        if ($this->getDocument()) {
            //now add recipients from emailDocument, if given.
            $storedRecipients = array_filter(explode(';', $this->getDocument()->getTo()));
            foreach ($storedRecipients as $multiRecipient) {
                $toRecipients[] = [$multiRecipient, ''];
            }
        }

        foreach ($toRecipients as $recipient) {
            $this->addTo($recipient[0], $recipient[1]);
        }
    }
}
