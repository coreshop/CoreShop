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

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use CoreShop\Bundle\FixtureBundle\Event\DataFixturesEvent;
use CoreShop\Bundle\FixtureBundle\Event\FixturesEvents;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\EntityManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DataFixturesExecutor implements DataFixturesExecutorInterface
{
    /** @var callable|null */
    private $logger;

    public function __construct(private EntityManager $em, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function execute(array $fixtures, $fixturesType)
    {
        $event = new DataFixturesEvent($this->em, $fixturesType, $this->logger);
        $this->eventDispatcher->dispatch($event, FixturesEvents::DATA_FIXTURES_PRE_LOAD);

        $executor = new ORMExecutor($this->em);
        if (null !== $this->logger) {
            $executor->setLogger($this->logger);
        }
        $executor->execute($fixtures, true);

        $this->eventDispatcher->dispatch($event, FixturesEvents::DATA_FIXTURES_POST_LOAD);
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
