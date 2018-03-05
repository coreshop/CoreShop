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

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class Currency extends AbstractResource implements CurrencyInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $isoCode;

    /**
     * @var int
     */
    protected $numericIsoCode;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getName(), $this->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param mixed $isoCode
     *
     * @return static
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $numericIsoCode
     *
     * @return static
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return static
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }
}
