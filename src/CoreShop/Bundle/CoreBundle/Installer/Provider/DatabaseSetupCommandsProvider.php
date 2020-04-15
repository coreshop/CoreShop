<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Installer\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DatabaseSetupCommandsProvider implements DatabaseSetupCommandsProviderInterface
{
    private $doctrineRegistry;

    public function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): array
    {
        return array_merge($this->getRequiredCommands($input, $output, $questionHelper), [
            'coreshop:install:fixtures',
        ]);
    }

    private function getRequiredCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): array
    {
        if ($input->getOption('no-interaction')) {
            $commands['coreshop:resources:drop-tables'] = ['application-name' => 'coreshop', '--force' => true];
        }

        return $this->setupDatabase($input, $output, $questionHelper);
    }

    private function setupDatabase(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): array
    {
        $outputStyle = new SymfonyStyle($input, $output);

        if (!$this->isSchemaPresent()) {
            return ['coreshop:resources:create-tables' => ['application-name' => 'coreshop', '--force' => true]];
        }

        $outputStyle->writeln('Seems like your database contains schema.');
        $outputStyle->writeln('<error>Warning! This action will erase your CoreShop Tables.</error>');
        $question = new ConfirmationQuestion('Do you want to reset your CoreShop scheme? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'coreshop:resources:drop-tables' => ['application-name' => 'coreshop', '--force' => true],
                'coreshop:resources:create-tables' => ['application-name' => 'coreshop', '--force' => true],
            ];
        }

        return [];
    }

    private function isSchemaPresent(): bool
    {
        return in_array('coreshop_store', $this->getSchemaManager()->listTableNames());
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        /**
         * @var EntityManager $manager
         */
        $manager = $this->doctrineRegistry->getManager();

        return $manager->getConnection()->getSchemaManager();
    }
}
