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
     * AddressFormatter constructor.
     *
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
        $objectVars = get_object_vars($address);
        $objectVars['country'] = $address->getCountry();

        //translate salutation
        if (!empty($address->getSalutation())) {
            $translationKey = 'coreshop.form.customer.salutation.'.$address->getSalutation();
            $objectVars['salutation'] = $this->translator->trans($translationKey);
        }

        $placeHolder = new Placeholder();
        $address = $placeHolder->replacePlaceholders($address->getCountry()->getAddressFormat(), $objectVars);

        if ($asHtml) {
            $address = nl2br($address);
        }

        return $address;
    }
}
