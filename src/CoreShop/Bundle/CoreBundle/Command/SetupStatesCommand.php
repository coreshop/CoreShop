<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Component\Address\Model\StateInterface;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Tool;
use Rinvex\Country\CountryLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
            ->setName('coreshop:setup:states')
            ->setDescription('Create states/regions for specified countries.')
            ->addArgument(
                'countries',
                InputArgument::REQUIRED,
                'Comma-separated list of country ISO codes (e.g., DE,US,FR)',
            )
            ->addOption(
                'activate-country',
                null,
                InputOption::VALUE_NONE,
                'Also activate the country if not already active',
            )
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates states/regions for specified countries.

Examples:
  <info>php bin/console %command.name% DE</info>
  <info>php bin/console %command.name% DE,US,FR</info>
  <info>php bin/console %command.name% DE --activate-country</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $countriesArg = $input->getArgument('countries');
        $activateCountry = $input->getOption('activate-country');
        $countryCodes = array_map('strtoupper', array_map('trim', explode(',', $countriesArg)));

        $languages = Tool::getValidLanguages();

        $io->title('CoreShop States Setup');
        $io->writeln(sprintf('Setting up states for countries: <info>%s</info>', implode(', ', $countryCodes)));

        $createdStates = 0;
        $skippedStates = 0;
        $countriesProcessed = 0;
        $activatedCountries = 0;

        foreach ($countryCodes as $countryCode) {
            $io->section(sprintf('Processing country: %s', $countryCode));

            // Check if country exists in database
            $country = $this->countryRepository->findByCode($countryCode);
            if ($country === null) {
                $io->warning(sprintf('Country with code "%s" not found in database. Run fixtures first or install CoreShop.', $countryCode));

                continue;
            }

            // Load country data from Rinvex
            try {
                $rinvexCountry = CountryLoader::country($countryCode);
            } catch (\Exception $e) {
                $io->warning(sprintf('Country data not found for code "%s" in Rinvex data: %s', $countryCode, $e->getMessage()));

                continue;
            }

            // Activate country if requested
            if ($activateCountry && !$country->getActive()) {
                $country->setActive(true);
                $this->entityManager->persist($country);
                $activatedCountries++;
                $io->writeln(sprintf('  <comment>Activated country: %s</comment>', $countryCode));
            }

            // Get divisions (states/regions)
            $divisions = $rinvexCountry->getDivisions();

            if (!is_array($divisions) || empty($divisions)) {
                $io->writeln(sprintf('  <comment>No divisions/states found for %s</comment>', $countryCode));
                $countriesProcessed++;

                continue;
            }

            foreach ($divisions as $isoCode => $division) {
                if (!$division['name']) {
                    continue;
                }

                // Check if state already exists
                $existingState = $this->stateRepository->findOneBy([
                    'isoCode' => $isoCode,
                    'country' => $country,
                ]);

                if ($existingState !== null) {
                    $skippedStates++;
                    $io->writeln(sprintf('  <comment>Skipping existing state: %s (%s)</comment>', $division['name'], $isoCode), OutputInterface::VERBOSITY_VERBOSE);

                    continue;
                }

                /**
                 * @var StateInterface $state
                 */
                $state = $this->stateFactory->createNew();

                foreach ($languages as $lang) {
                    $state->setName($division['name'], $lang);
                }

                $state->setIsoCode($isoCode);
                $state->setCountry($country);
                $state->setActive(true);

                $this->entityManager->persist($state);
                $createdStates++;

                $io->writeln(sprintf('  <info>Created state: %s (%s)</info>', $division['name'], $isoCode), OutputInterface::VERBOSITY_VERBOSE);
            }

            $countriesProcessed++;
        }

        $this->entityManager->flush();

        $io->success([
            sprintf('Countries processed: %d', $countriesProcessed),
            sprintf('States created: %d', $createdStates),
            sprintf('States skipped (already exist): %d', $skippedStates),
            sprintf('Countries activated: %d', $activatedCountries),
        ]);

        return Command::SUCCESS;
    }
}
