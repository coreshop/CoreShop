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

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class GeoLiteBasedRequestResolver implements RequestResolverInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param SessionInterface           $session
     * @param string                     $sessionKeyName
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(
        SessionInterface $session,
        string $sessionKeyName,
        CountryRepositoryInterface $countryRepository
    ) {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $record = null;
        $isoCode = null;
        $clientIp = $request->getClientIp();

        if ($this->checkIfIpIsPrivate($clientIp)) {
            throw new CountryNotFoundException();
        }

        $countryIsoCode = $this->guessCountryBySession($clientIp);

        if (is_null($countryIsoCode)) {
            $countryIsoCode = $this->guessCountryByGeoLite($clientIp);
        }

        if (is_null($countryIsoCode)) {
            throw new CountryNotFoundException();
        }

        $country = $this->countryRepository->findByCode($countryIsoCode);
        if (!$country instanceof CountryInterface) {
            throw new CountryNotFoundException();
        }

        $this->storeData($country, $clientIp);

        return $country;
    }

    /**
     * @param string $clientIp
     *
     * @return string|null
     */
    private function guessCountryBySession($clientIp)
    {
        $isoCode = null;
        $clientIpHash = md5($clientIp);
        $sessionKey = sprintf('%s.%s', $this->sessionKeyName, $clientIpHash);

        if ($this->session->has($sessionKey)) {
            $isoCode = $this->session->get($sessionKey);
        }

        return $isoCode;

    }

    /**
     * @param string $clientIp
     *
     * @return string|null
     */
    private function guessCountryByGeoLite($clientIp)
    {
        $isoCode = null;
        $geoDbFile = PIMCORE_CONFIGURATION_DIRECTORY . '/GeoLite2-City.mmdb';

        if (!file_exists($geoDbFile)) {
            return null;
        }

        try {
            $reader = new Reader($geoDbFile);
            $record = $reader->city($clientIp);
            $isoCode = $record->country->isoCode;
        } catch (\Exception $e) {
            //If something goes wrong, ignore the exception and throw a CountryNotFoundException
        }

        return $isoCode;
    }

    /**
     * @param CountryInterface $country
     * @param string           $clientIp
     */
    private function storeData(CountryInterface $country, $clientIp)
    {
        $clientIpHash = md5($clientIp);
        $sessionKey = sprintf('%s.%s', $this->sessionKeyName, $clientIpHash);

        $this->session->set($sessionKey, $country->getIsoCode());
    }

    /**
     * Check if ip is private.
     *
     * @param string $clientIp
     *
     * @return bool
     */
    private function checkIfIpIsPrivate($clientIp)
    {
        $privateAddresses = [
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255', // localhost
        ];

        $longIp = ip2long($clientIp);
        if ($longIp != -1) {
            foreach ($privateAddresses as $priAddr) {
                list($start, $end) = explode('|', $priAddr);

                // IF IS PRIVATE
                if ($longIp >= ip2long($start) && $longIp <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }
}
