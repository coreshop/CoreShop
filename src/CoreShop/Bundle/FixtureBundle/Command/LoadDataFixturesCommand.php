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

namespace CoreShop\Bundle\FixtureBundle\Command;

use CoreShop\Bundle\FixtureBundle\Fixture\DataFixturesExecutorInterface;
use CoreShop\Bundle\FixtureBundle\Fixture\Loader\DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class LoadDataFixturesCommand extends Command
{
    public const COMMAND_NAME = 'coreshop:fixture:data:load';

    public const MAIN_FIXTURES_TYPE = DataFixturesExecutorInterface::MAIN_FIXTURES;

    public const DEMO_FIXTURES_TYPE = DataFixturesExecutorInterface::DEMO_FIXTURES;

    public const MAIN_FIXTURES_PATH = 'Fixtures/Data/Application';

    public const DEMO_FIXTURES_PATH = 'Fixtures/Data/Demo';

    public function __construct(protected DataFixturesLoader $fixtureLoader, protected DataFixturesExecutorInterface $fixtureExecutor)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription('Load data fixtures.')
            ->addOption(
                'fixtures-type',
                null,
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Select fixtures type to be loaded (%s or %s). By default - %s',
                    self::MAIN_FIXTURES_TYPE,
                    self::DEMO_FIXTURES_TYPE,
                    self::MAIN_FIXTURES_TYPE
                ),
                self::MAIN_FIXTURES_TYPE
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs list of fixtures without apply them')
            ->addOption(
                'bundles',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'A list of bundle names to load data from'
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'A list of bundle names which fixtures should be skipped'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixtures = null;

        try {
            $fixtures = $this->getFixtures($input, $output);
        } catch (\RuntimeException $ex) {
            $output->writeln('');
            $output->writeln(sprintf('<error>%s</error>', $ex->getMessage()));

            return 1;
        }

        if (!empty($fixtures)) {
            if ($input->getOption('dry-run')) {
                $this->outputFixtures($input, $output, $fixtures);
            } else {
                $this->processFixtures($input, $output, $fixtures);
            }
        }

        return 0;
    }

    /**
     * @return array
     *
     * @throws \RuntimeException if loading of data fixtures should be terminated
     */
    protected function getFixtures(InputInterface $input, OutputInterface $output)
    {
        $bundles = $input->getOption('bundles');
        $excludeBundles = $input->getOption('exclude');
        $fixtureRelativePath = $this->getFixtureRelativePath($input);

        /**
         * @var Application $application
         */
        $application = $this->getApplication();

        /** @var BundleInterface $bundle */
        foreach ($application->getKernel()->getBundles() as $bundle) {
            if (!empty($bundles) && !in_array($bundle->getName(), $bundles)) {
                continue;
            }
            if (!empty($excludeBundles) && in_array($bundle->getName(), $excludeBundles)) {
                continue;
            }
            $path = $bundle->getPath() . $fixtureRelativePath;
            if (is_dir($path)) {
                $this->fixtureLoader->loadFromDirectory($path);
            }
        }

        return $this->fixtureLoader->getFixtures();
    }

    /**
     * Output list of fixtures.
     *
     * @param array           $fixtures
     */
    protected function outputFixtures(InputInterface $input, OutputInterface $output, $fixtures)
    {
        $output->writeln(
            sprintf(
                'List of "%s" data fixtures ...',
                $this->getTypeOfFixtures($input)
            )
        );
        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $fixture::class));
        }
    }

    /**
     * Process fixtures.
     *
     * @param array           $fixtures
     */
    protected function processFixtures(InputInterface $input, OutputInterface $output, $fixtures)
    {
        $output->writeln(
            sprintf(
                'Loading "%s" data fixtures ...',
                $this->getTypeOfFixtures($input)
            )
        );

        $this->fixtureExecutor->setLogger(
            function (string $message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            }
        );
        $this->fixtureExecutor->execute($fixtures, $this->getTypeOfFixtures($input));
    }

    protected function getTypeOfFixtures(InputInterface $input): string
    {
        return (string)$input->getOption('fixtures-type');
    }

    /**
     * @return string
     */
    protected function getFixtureRelativePath(InputInterface $input)
    {
        $fixtureRelativePath = $this->getTypeOfFixtures($input) == self::DEMO_FIXTURES_TYPE
            ? self::DEMO_FIXTURES_PATH
            : self::MAIN_FIXTURES_PATH;

        return str_replace('/', \DIRECTORY_SEPARATOR, '/' . $fixtureRelativePath);
    }
}
