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

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend\Checkout;

use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;

class SummaryPage extends AbstractFrontendPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_checkout';
    }

    public function submitOrder(): void
    {
        $this->getElement('submit_order')->click();
    }

    public function acceptTermsOfService(): void
    {
        $this->getElement('terms_of_service')->check();
    }

    public function declineTermsOfService(): void
    {
        $this->getElement('terms_of_service')->uncheck();
    }

    public function submitQuote(): void
    {
        $this->getElement('submit_quote')->click();
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
