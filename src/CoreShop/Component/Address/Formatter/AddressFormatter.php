<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressInterface;
use Pimcore\Placeholder;
use Symfony\Component\Translation\TranslatorInterface;

class AddressFormatter implements AddressFormatterInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAddress(AddressInterface $address, $asHtml = true)
    {
        if (method_exists($address, 'getObjectVars')) {
            $objectVars = $address->getObjectVars();
        } else {
            $objectVars = get_object_vars($address);
        }

        $objectVars['country'] = $address->getCountry();
        $objectVars['state'] = $address->getState();

        //translate salutation
        if (!empty($address->getSalutation())) {
            $translationKey = 'coreshop.form.customer.salutation.' . $address->getSalutation();
            $objectVars['salutation'] = $this->translator->trans($translationKey);
        }

        $placeHolder = new Placeholder();

        $address = $placeHolder->replacePlaceholders($address->getCountry()->getAddressFormat(), $objectVars);

        if ($asHtml) {
            $address = nl2br($this->removeEmptyLines($address));
        }

        return $address;
    }

    /**
     * @param string $payload
     * @return string
     */
    private function removeEmptyLines(string $payload) : string
    {
        $values = array_filter(explode( "\n", $payload), function ($value) {
            return !empty(trim($value));
        });

        return implode("\n", $values);
    }
}
