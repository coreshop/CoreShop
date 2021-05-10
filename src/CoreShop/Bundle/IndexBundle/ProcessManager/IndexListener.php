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

namespace CoreShop\Bundle\IndexBundle\ProcessManager;

use Carbon\Carbon;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use ProcessManagerBundle\Factory\ProcessFactoryInterface;
use ProcessManagerBundle\Logger\ProcessLogger;
use ProcessManagerBundle\Model\ProcessInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class IndexListener
{
    private ProcessInterface $process;
    private ProcessFactoryInterface $processFactory;
    private ProcessLogger $processLogger;

    public function __construct(FactoryInterface $processFactory, ProcessLogger $processLogger)
    {
        $this->processFactory = $processFactory;
        $this->processLogger = $processLogger;
    }

    /**
     * @param GenericEvent $event
     */
    public function onClasssesEvent(GenericEvent $event)
    {
        if (null === $this->process) {
            $date = Carbon::now();

            $this->process = $this->processFactory->createProcess(
                sprintf(
                    'CoreShop Index: %s',
                    $date->formatLocalized('%A %d %B %Y')
                ),
                'coreshop_index',
                'Indexing',
                -1,
                0
            );
            $this->process->save();

            $this->processLogger->info($this->process, $event->getSubject());
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function onStartEvent(GenericEvent $event)
    {
        if ($this->process) {
            $this->process->setTotal($event->getSubject());
            $this->process->save();

            $this->processLogger->info($this->process, 'Total to Process: ' . $event->getSubject());
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function onProgressEvent(GenericEvent $event)
    {
        if ($this->process) {
            $this->process->progress();
            $this->process->save();

            $this->processLogger->info($this->process, $event->getSubject());
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function onStatusEvent(GenericEvent $event)
    {
        if ($this->process) {
            $this->process->setMessage($event->getSubject());
            $this->process->save();

            $this->processLogger->info($this->process, $event->getSubject());
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function onFinishedEvent(GenericEvent $event)
    {
        if ($this->process) {
            $this->process->setProgress($this->process->getTotal());
            $this->process->save();

            $this->processLogger->info($this->process, $event->getSubject());
        }
    }
}
