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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use Webmozart\Assert\Assert;

final class CountryContext implements Context
{
    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Transform /^country "([^"]+)"$/
     */
    public function getCountryByName($name)
    {
        $countries = $this->countryRepository->findByName($name, 'en');

        Assert::eq(
            count($countries),
            1,
            sprintf('%d country has been found with name "%s".', count($countries), $name)
        );

        return reset($countries);
    }
}
