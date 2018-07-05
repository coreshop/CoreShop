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

namespace CoreShop\Bundle\CoreBundle\Command;

use CoreShop\Bundle\CoreBundle\Rule\RuleAvailabilityCheckInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RuleAvailabilityCommand extends Command
{
    /**
     * @var RuleAvailabilityCheckInterface
     */
    protected $ruleAvailabilityCheck;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param RuleAvailabilityCheckInterface $ruleAvailabilityCheck
     * @param array                          $params
     */
    public function __construct(RuleAvailabilityCheckInterface $ruleAvailabilityCheck, $params = [])
    {
        $this->ruleAvailabilityCheck = $ruleAvailabilityCheck;
        $this->params = $params;

        parent::__construct();
    }

    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:check-rule-availability')
            ->setDescription('Check for outdated / invalid rules and disable them.');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this->params;
        $this->ruleAvailabilityCheck->check($params);
        return 0;
    }
}
