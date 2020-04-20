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

namespace CoreShop\Component\SEO;

use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use CoreShop\Component\SEO\Schema\SchemaGeneratorInterface;
use Spatie\SchemaOrg\Graph;

class SEOSchemaPresentation implements SEOSchemaPresentationInterface
{
    protected $schemaGeneratorRegistry;

    public function __construct(PrioritizedServiceRegistryInterface $schemaGeneratorRegistry)
    {
        $this->schemaGeneratorRegistry = $schemaGeneratorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createSchema($object): Graph
    {
        $graph = new Graph();

        /**
         * @var SchemaGeneratorInterface $schemaGenerator
         */
        foreach ($this->schemaGeneratorRegistry->all() as $schemaGenerator) {
            if ($schemaGenerator->supports($object)) {
                $schemaGenerator->generateSchema($graph, $object);
            }
        }

        return $graph;
    }
}
