<?php

namespace CoreShop\Bundle\TaxationBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Resource\Exception\UnexpectedTypeException;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Taxation\Model\TaxRule;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Model\TaxRuleInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class TaxRuleGroupPreSaveListener
{
    /**
     * @var FactoryInterface
     */
    private $taxRuleFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var RepositoryInterface
     */
    private $stateRepository;

    /**
     * @param FactoryInterface $taxRuleFactory
     * @param RepositoryInterface $taxRuleRepository
     * @param EntityManagerInterface $entityManager
     * @param RepositoryInterface $countryRepository,
     * @param RepositoryInterface $stateRepository,
     * @param RepositoryInterface $taxRateRepository
     */
    public function __construct(
        FactoryInterface $taxRuleFactory,
        RepositoryInterface $taxRuleRepository,
        EntityManagerInterface $entityManager,
        RepositoryInterface $countryRepository,
        RepositoryInterface $stateRepository,
        RepositoryInterface $taxRateRepository)
    {
        $this->taxRuleFactory = $taxRuleFactory;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->entityManager = $entityManager;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     *
     * @param ResourceControllerEvent $event
     */
    public function onTaxRuleGroupPreSave(ResourceControllerEvent $event)
    {
        $resource = $event->getSubject();
        /**
         * @var $request Request
         */
        $request = $event->getArgument('request');

        if (!$resource instanceof TaxRuleGroupInterface) {
            throw new UnexpectedTypeException(
                $resource,
                TaxRuleGroupInterface::class
            );
        }

        /**
         * I think we should this via FORMS, so all of this stuff could be done automatically
         */

        $data = $request->get('taxRules');
        $taxRules = json_decode($data, true);
        $taxRulesUpdated = [];
        $newTaxRules = [];

        foreach ($taxRules as $taxRule) {
            $id = intval($taxRule['id']);
            $taxRuleObject = null;

            unset($taxRule['id']);

            if ($id) {
                $taxRuleObject = $this->taxRuleRepository->find($id);
            }

            if (!$taxRuleObject instanceof TaxRuleInterface) {
                $taxRuleObject = $this->taxRuleFactory->createNew();
            }

            if ($taxRule['country']) {
                $country = $this->countryRepository->find($taxRule['country']);

                if ($country) {
                    $taxRuleObject->setCountry($country);
                }
            }

            if ($taxRule['state']) {
                $state = $this->stateRepository->find($taxRule['state']);

                if ($state) {
                    $taxRuleObject->setState($state);
                }
            }

            if ($taxRule['taxRate']) {
                $taxRate = $this->taxRateRepository->find($taxRule['taxRate']);

                if ($taxRate) {
                    $taxRuleObject->setTaxRate($taxRate);
                }
            }

            $taxRuleObject->setBehavior($taxRule['behavior']);

            $this->entityManager->persist($taxRuleObject);

            $taxRulesUpdated[] = $taxRuleObject->getId();
            $newTaxRules[] = $taxRuleObject;
        }

        $taxRules = $resource->getTaxRules();

        foreach ($taxRules as $rule) {
            if (!in_array($rule->getId(), $taxRulesUpdated)) {
                $this->entityManager->remove($rule);
            }
        }

        foreach ($newTaxRules as $taxRuleObject) {
            $taxRuleObject->setTaxRuleGroup($resource);
        }
    }
}
