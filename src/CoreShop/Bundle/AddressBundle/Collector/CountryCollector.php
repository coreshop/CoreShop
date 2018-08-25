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

namespace CoreShop\Bundle\AddressBundle\Collector;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class CountryCollector extends DataCollector
{
    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param CountryContextInterface $countryContext
     * @param bool $countryChangeSupport
     */
    public function __construct(
        CountryContextInterface $countryContext,
        $countryChangeSupport = false
    )
    {
        $this->countryContext = $countryContext;

        $this->data = [
            'country' => null,
            'country_change_support' => $countryChangeSupport,
        ];
    }

    /**
     * @return CountryInterface
     */
    public function getCountry()
    {
        return $this->data['country'];
    }

    /**
     * @return bool
     */
    public function isCountryChangeSupported()
    {
        return $this->data['country_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        try {
            $this->data['country'] = $this->countryContext->getCountry();
            $this->data['countryName'] = $this->data['country']->getName();
        } catch (\Exception $exception) {
            //If something went wrong, we don't have any country, which we can safely ignore
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.country_collector';
    }
}
