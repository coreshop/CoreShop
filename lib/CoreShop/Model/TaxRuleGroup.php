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

class TaxRuleGroup extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $active;

    /**
     * Return all TaxRules.
     *
     * @return TaxRule[]
     */
    public function getRules()
    {
        $listing = new TaxRule\Listing();
        $listing->setCondition('taxRuleGroupId = ?', array($this->getId()));

        return $listing->getData();
    }

    /**
     * Return all TaxRules for a Country.
     *
     * @param Country $country|null
     * @param State   $state|null
     *
     * @return TaxRule[]
     */
    public function getForCountryAndState($country, $state)
    {
        $queryParams = array($this->getId());

        if ($country instanceof Country) {
            $queryParams[] = intval($country->getId());
        } else {
            $queryParams[] = 0;
        }

        //Add All for country
        $queryParams[] = 0;

        if ($state instanceof State) {
            $queryParams[] = intval($state->getId());
        } else {
            $queryParams[] = 0;
        }

        //Add All for State
        $queryParams[] = 0;

        $listing = new TaxRule\Listing();
        $listing->setCondition('taxRuleGroupId = ? AND countryId IN(?, ?) AND stateId IN(?, ?)', $queryParams);

        return $listing->getData();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
