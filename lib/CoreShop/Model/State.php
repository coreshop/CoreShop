<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model;

class State extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $active;

    /**
     * @var Country
     */
    public $country;

    /**
     * @var int
     */
    public $countryId;

    /**
     * Get State by ISO-Code.
     *
     * @param $isoCode
     *
     * @return Country|null
     */
    public static function getByIsoCode($isoCode)
    {
        return parent::getByField('isoCode', $isoCode);
    }

    /**
     * Get all states for a country.
     *
     * @param int|Country $country
     *
     * @return State[]
     */
    public static function getForCountry($country)
    {
        $countryId = $country;

        if ($country instanceof Country) {
            $countryId = $country->getId();
        }

        $list = State::getList();
        $list->setCondition('countryId = ?', array($countryId));

        return $list->getData();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     */
    public function setActive($active)
    {
        if (is_bool($active)) {
            if ($active) {
                $active = 1;
            } else {
                $active = 0;
            }
        }
        $this->active = $active;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        if (!$this->country instanceof Country) {
            $this->country = Country::getById($this->countryId);
        }

        return $this->country;
    }

    /**
     * @param $country
     *
     * @throws \Exception
     */
    public function setCountry($country)
    {
        if (!$country instanceof Country) {
            throw new \Exception('$country must be instance of Country');
        }

        $this->country = $country;
        $this->countryId = $country->getId();
    }

    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param $countryId
     *
     * @throws \Exception
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getName());
    }
}
