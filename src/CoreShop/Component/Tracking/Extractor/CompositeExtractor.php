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

namespace CoreShop\Component\Tracking\Extractor;

use CoreShop\Component\Registry\ServiceRegistryInterface;

class CompositeExtractor implements TrackingExtractorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $extractorRegistry;

    /**
     * @param ServiceRegistryInterface $extractorRegistry
     */
    public function __construct(ServiceRegistryInterface $extractorRegistry)
    {
        $this->extractorRegistry = $extractorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetadata($object, $data = []): array
    {
        /**
         * @var TrackingExtractorInterface $extractor
         */
        foreach ($this->extractorRegistry->all() as $extractor) {
            if ($extractor->supports($object)) {
                $data = $extractor->updateMetadata($object, $data);
            }
        }

        return $data;
    }
}
