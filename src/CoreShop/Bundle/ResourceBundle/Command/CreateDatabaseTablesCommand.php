<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateDatabaseTablesCommand extends Command
{
    /**
     * @var array
     */
    private $coreShopResources;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param array                  $coreShopResources
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(array $coreShopResources, EntityManagerInterface $entityManager)
    {
        $this->coreShopResources = $coreShopResources;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:resources:create-tables')
            ->setDescription('Create Tables.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates Database Tables
EOT
            )
            ->addArgument(
                'application-name',
                InputArgument::REQUIRED
            )
            ->addOption(
                'dump-sql',
                null,
                InputOption::VALUE_NONE,
                'Dumps the generated SQL statements to the screen (does not execute them).'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Causes the generated SQL statements to be physically executed against your database.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ui = new SymfonyStyle($input, $output);

        $resources = $this->coreShopResources;
        $em = $this->entityManager;

        $metadatas = [];

        foreach ($resources as $alias => $resource) {
            $applicationName = explode('.', $alias)[0];

            if ($applicationName === $input->getArgument('application-name')) {
                $metadatas[] = $em->getMetadataFactory()->getMetadataFor($resource['classes']['model']);
            }
        }

        $schemaTool = new SchemaTool($em);
        $sqls = $schemaTool->getUpdateSchemaSql($metadatas, true);

        $dumpSql = true === $input->getOption('dump-sql');
        $force = true === $input->getOption('force');

        if ($dumpSql) {
            $ui->text('The following SQL statements will be executed:');
            $ui->newLine();

            foreach ($sqls as $sql) {
                $ui->text(sprintf('    %s;', $sql));
            }
        }

        if ($force) {
            if ($dumpSql) {
                $ui->newLine();
            }
            $ui->text('Updating database schema...');
            $ui->newLine();

            $schemaTool->updateSchema($metadatas, true);

            $pluralization = (1 === count($sqls)) ? 'query was' : 'queries were';

            $ui->text(sprintf('    <info>%s</info> %s executed', count($sqls), $pluralization));
            $ui->success('Database schema updated successfully!');
        }

        if ($dumpSql || $force) {
            return 0;
        }

        return 0;
    }
}
