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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineORMContext implements Context
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(): void
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
        $this->entityManager->clear();
    }
}
