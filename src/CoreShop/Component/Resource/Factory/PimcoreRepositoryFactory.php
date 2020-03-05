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

namespace CoreShop\Component\Resource\Factory;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;

class PimcoreRepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var string
     */
    private $repositoryClassName;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param string            $repositoryClassName
     * @param MetadataInterface $metadata
     * @param Connection        $connection
     */
    public function __construct(string $repositoryClassName, MetadataInterface $metadata, Connection $connection)
    {
        $this->repositoryClassName = $repositoryClassName;
        $this->metadata = $metadata;
        $this->connection = $connection;
    }

    public function createNewRepository(ObjectManager $objectManager): RepositoryInterface
    {
        $repositoryClass = $this->repositoryClassName;

        return new $repositoryClass($this->metadata, $this->connection);
    }
}
