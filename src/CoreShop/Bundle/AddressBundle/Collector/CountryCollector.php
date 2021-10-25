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

namespace CoreShop\Bundle\AddressBundle\Collector;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class CountryCollector extends DataCollector
{
    public function __construct(
        private CountryContextInterface $countryContext,
        private PimcoreContextResolver $pimcoreContext,
        $countryChangeSupport = false
    ) {
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

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($this->pimcoreContext->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            $this->data['admin'] = true;

            return;
        }

        try {
            $this->data['country'] = $this->countryContext->getCountry();
            $this->data['country_name'] = $this->countryContext->getCountry()->getName();
            $this->data['country_change_support'] = $this->isCountryChangeSupported();
        } catch (\Exception) {
            //If something went wrong, we don't have any country, which we can safely ignore
        }
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'coreshop.country_collector';
    }
}
