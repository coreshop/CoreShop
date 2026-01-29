<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Tool;
use Rinvex\Country\CountryLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'coreshop:setup:states',
    description: 'Setup states/regions for countries.',
)]
final class SetupStatesCommand extends Command
{
    public function __construct(
        private CountryRepositoryInterface $countryRepository,
        private RepositoryInterface $stateRepository,
        private FactoryInterface $stateFactory,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'countries',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'ISO-2 country codes to setup states for (e.g., AT DE US)',
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Setup states for all existing countries',
            )
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates states/regions for specified countries.

Examples:
  <info>php %command.full_name% AT DE</info>           Setup states for Austria and Germany
  <info>php %command.full_name% --all</info>           Setup states for all countries
  <info>php %command.full_name% US CA MX</info>        Setup states for USA, Canada, and Mexico
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $countryCodes = $input->getArgument('countries');
        $setupAll = $input->getOption('all');

        if (empty($countryCodes) && !$setupAll) {
            $io->error('Please specify country codes or use --all option.');

            return Command::INVALID;
        }

        $languages = Tool::getValidLanguages();

        if ($setupAll) {
            $countries = $this->countryRepository->findAll();
        } else {
            $countries = [];
            foreach ($countryCodes as $code) {
                $country = $this->countryRepository->findByCode(strtoupper($code));
                if (null === $country) {
                    $io->warning(sprintf('Country with code "%s" not found in database. Skipping.', $code));

                    continue;
                }
                $countries[] = $country;
            }
        }

        if (empty($countries)) {
            $io->error('No valid countries found to process.');

            return Command::FAILURE;
        }

        $io->title('Setting up states for countries');

        $totalStatesCreated = 0;
        $totalStatesSkipped = 0;

        foreach ($countries as $country) {
            /** @var CountryInterface $country */
            $countryCode = $country->getIsoCode();
            $countryData = CountryLoader::country($countryCode);

            if (null === $countryData) {
                $io->warning(sprintf('No data found for country "%s" (%s) in Rinvex library. Skipping.', $country->getName(), $countryCode));

                continue;
            }

            $divisions = $countryData->getDivisions();

            if (!is_array($divisions) || empty($divisions)) {
                $io->note(sprintf('Country "%s" (%s) has no divisions/states.', $country->getName(), $countryCode));

                continue;
            }

            $io->section(sprintf('Processing %s (%s) - %d divisions found', $country->getName(), $countryCode, count($divisions)));

            $statesCreated = 0;
            $statesSkipped = 0;

            foreach ($divisions as $isoCode => $division) {
                if (empty($division['name'])) {
                    continue;
                }

                // Check if state already exists
                $existingState = $this->stateRepository->findOneBy(['isoCode' => $isoCode]);
                if (null !== $existingState) {
                    $statesSkipped++;

                    continue;
                }

                /** @var StateInterface $state */
                $state = $this->stateFactory->createNew();

                foreach ($languages as $lang) {
                    $state->setName($division['name'], $lang);
                }

                $state->setIsoCode($isoCode);
                $state->setCountry($country);
                $state->setActive(true);

                $this->entityManager->persist($state);
                $statesCreated++;
            }

            if ($statesCreated > 0 || $statesSkipped > 0) {
                $io->text(sprintf('  Created: %d, Skipped (already exist): %d', $statesCreated, $statesSkipped));
            }

            $totalStatesCreated += $statesCreated;
            $totalStatesSkipped += $statesSkipped;
        }

        $this->entityManager->flush();

        $io->newLine();
        $io->success(sprintf(
            'States setup complete. Total created: %d, Total skipped: %d',
            $totalStatesCreated,
            $totalStatesSkipped,
        ));

        return Command::SUCCESS;
    }
}
