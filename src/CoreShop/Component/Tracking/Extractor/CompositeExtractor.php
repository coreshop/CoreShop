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

namespace CoreShop\Component\Tracking\Extractor;

use CoreShop\Component\Registry\ServiceRegistryInterface;

class CompositeExtractor implements TrackingExtractorInterface
{
    private ServiceRegistryInterface $extractorRegistry;

    public function __construct(ServiceRegistryInterface $extractorRegistry)
    {
        $this->extractorRegistry = $extractorRegistry;
    }

    public function supports($object): bool
    {
        return true;
    }

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
