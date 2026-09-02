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

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Bundle\TestBundle\Page\Frontend\AbstractFrontendPage;
use CoreShop\Bundle\TestBundle\Service\DriverHelper;

class SummaryPage extends AbstractFrontendPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_checkout';
    }

    public function submitOrder(): void
    {
        $this->getElement('submit_order')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function acceptTermsOfService(): void
    {
        $this->getElement('terms_of_service')->click();
    }

    public function declineTermsOfService(): void
    {
        $this->getElement('terms_of_service')->click();
    }

    public function submitQuote(): void
    {
        $this->getElement('submit_quote')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    protected function getAdditionalParameters(): array
    {
        return [
            'stepIdentifier' => 'summary',
        ];
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'submit_order' => '[data-test-submit-order]',
            'submit_quote' => '[data-test-submit-quote]',
            'terms_of_service' => '[data-test-accept-terms]',
        ]);
    }
}
