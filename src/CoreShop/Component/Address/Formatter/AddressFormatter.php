<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Sandbox\SandboxInterface;

class AddressFormatter implements AddressFormatterInterface
{
    private SandboxInterface $sandbox;

    /**
     * @param SandboxInterface|Environment $sandbox The sandbox the address format is rendered in
     *                                              (see AddressFormatSandboxFactory). A Twig environment
     *                                              is ignored and replaced by the default sandbox, since the
     *                                              address format is user-editable and must never be rendered
     *                                              unrestricted.
     */
    public function __construct(
        SandboxInterface|Environment $sandbox,
        private TranslatorInterface $translator,
    ) {
        if ($sandbox instanceof Environment) {
            trigger_deprecation(
                'coreshop/address',
                '5.0',
                'Passing a Twig environment to "%s" is deprecated and ignored for security reasons. Pass a "%s" created by "%s::create()" instead.',
                self::class,
                SandboxInterface::class,
                AddressFormatSandboxFactory::class,
            );

            $sandbox = AddressFormatSandboxFactory::create($translator);
        }

        $this->sandbox = $sandbox;
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

        $convertedAddress = $this->sandbox->createTemplate((string) $address->getCountry()?->getAddressFormat())->render($objectVars);

        if ($asHtml) {
            $convertedAddress = \nl2br($convertedAddress);
        }

        return $convertedAddress;
    }
}
