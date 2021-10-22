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

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Resource\Model\TimestampableTrait;
use CoreShop\Component\Resource\Model\ToggleableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class TaxRuleGroup extends AbstractResource implements TaxRuleGroupInterface, \Stringable
{
    use ToggleableTrait;

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
     * @var Collection|TaxRuleInterface[]
     */
    protected $taxRules;

    public function __construct()
    {
        $this->taxRules = new ArrayCollection();
    }

    public function __toString(): string
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTaxRules()
    {
        return $this->taxRules;
    }

    public function hasTaxRules()
    {
        return !$this->taxRules->isEmpty();
    }

    public function addTaxRule(TaxRuleInterface $taxRule)
    {
        if (!$this->hasTaxRule($taxRule)) {
            $this->taxRules->add($taxRule);

            $taxRule->setTaxRuleGroup($this);
        }
    }

    public function removeTaxRule(TaxRuleInterface $taxRule)
    {
        if ($this->hasTaxRule($taxRule)) {
            $this->taxRules->removeElement($taxRule);
            $taxRule->setTaxRuleGroup(null);
        }
    }

    public function hasTaxRule(TaxRuleInterface $taxRule)
    {
        return $this->taxRules->contains($taxRule);
    }
}
