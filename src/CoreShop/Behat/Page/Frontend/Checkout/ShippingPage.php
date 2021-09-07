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

class ShippingPage extends AbstractFrontendPage implements ShippingPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_checkout';
    }

    public function submitStep(): void
    {
        $this->getElement('submit_shipping_step')->click();
    }

    public function getCarriers(): array
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '[data-test-carrier-label]');

        $carriers = [];

        foreach ($inputs as $input) {
            $carriers[] = trim($input->getText());
        }

        return $carriers;
    }

    protected function getAdditionalParameters(): array
    {
        return [
            'stepIdentifier' => 'shipping'
        ];
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'submit_shipping_step' => '[data-test-submit-shipping-step]',
        ]);
    }
}
