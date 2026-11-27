<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AddressFormatter implements AddressFormatterInterface
{
    private Environment $twig;

    /**
     * @param Environment $twig A sandboxed environment (see AddressFormatSandboxFactory). Any other
     *                          environment is replaced by the default sandbox, since the address
     *                          format is user-editable and must never be rendered unrestricted.
     */
    public function __construct(
        Environment $twig,
        private TranslatorInterface $translator,
    ) {
        if (!AddressFormatSandboxFactory::isSandboxed($twig)) {
            trigger_deprecation(
                'coreshop/address',
                '4.1',
                'Passing a non-sandboxed Twig environment to "%s" is deprecated and ignored for security reasons. Use "%s::create()" instead.',
                self::class,
                AddressFormatSandboxFactory::class,
            );

            $twig = AddressFormatSandboxFactory::create($translator);
        }

        $this->twig = $twig;
    }

    public function formatAddress(AddressInterface $address, bool $asHtml = true): string
    {
        if (method_exists($address, 'getObjectVars')) {
            $objectVars = $address->getObjectVars();
        } else {
            $objectVars = get_object_vars($address);
        }

        $objectVars['country'] = $address->getCountry();

        //translate salutation
        if (!empty($address->getSalutation())) {
            $translationKey = 'coreshop.form.customer.salutation.' . $address->getSalutation();
            $objectVars['salutation'] = $this->translator->trans($translationKey);
        }

        $convertedAddress = $this->twig->createTemplate((string) $address->getCountry()?->getAddressFormat())->render($objectVars);

        if ($asHtml) {
            $convertedAddress = \nl2br($convertedAddress);
        }

        return $convertedAddress;
    }
}
