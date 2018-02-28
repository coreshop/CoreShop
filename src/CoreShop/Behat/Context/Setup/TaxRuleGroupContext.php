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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class TaxRuleGroupContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $taxRuleGroupFactory;

    /**
     * @var FactoryInterface
     */
    private $taxRuleFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxRuleGroupRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $taxRuleGroupFactory
     * @param FactoryInterface $taxRuleFactory
     * @param RepositoryInterface $taxRuleGroupRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ObjectManager $objectManager,
        FactoryInterface $taxRuleGroupFactory,
        FactoryInterface $taxRuleFactory,
        RepositoryInterface $taxRuleGroupRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->objectManager = $objectManager;
        $this->taxRuleGroupFactory = $taxRuleGroupFactory;
        $this->taxRuleFactory = $taxRuleFactory;
        $this->taxRuleGroupRepository = $taxRuleGroupRepository;
    }

    /**
     * @Given /^the site has a tax rule "([^"]+)"$/
     */
    public function theSiteHasATaxRuleGroup($name)
    {
        $this->createTaxRuleGroup($name);
    }

    /**
     * @param $name
     */
    private function createTaxRuleGroup($name)
    {
        /**
         * @var $taxRule TaxRuleGroupInterface
         */
        $taxRule = $this->taxRuleGroupFactory->createNew();
        $taxRule->setName($name);

        $this->saveTaxRuleGroup($taxRule);
    }

    /**
     * @param TaxRuleGroupInterface $taxRuleGroup
     */
    private function saveTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup)
    {
        $this->objectManager->persist($taxRuleGroup);
        $this->objectManager->flush();

        $this->sharedStorage->set('taxRuleGroup', $taxRuleGroup);
    }
}
