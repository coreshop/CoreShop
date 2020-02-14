<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\AddressBundle\Collector;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class CountryCollector extends DataCollector
{
    private $countryContext;

    public function __construct(
        CountryContextInterface $countryContext,
        $countryChangeSupport = false
    ) {
        $this->countryContext = $countryContext;

        $this->data = [
            'country' => null,
            'country_change_support' => $countryChangeSupport,
        ];
    }

    public function getCountry(): ?CountryInterface
    {
        return $this->data['country'];
    }

    public function isCountryChangeSupported(): bool
    {
        return $this->data['country_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        try {
            $this->data['country'] = $this->countryContext->getCountry();
            $this->data['country_change_support'] = $this->isCountryChangeSupported();
        } catch (\Exception $exception) {
            //If something went wrong, we don't have any country, which we can safely ignore
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'coreshop.country_collector';
    }
}
