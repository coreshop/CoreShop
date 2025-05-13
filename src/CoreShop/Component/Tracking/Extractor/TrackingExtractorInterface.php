<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Tracking\Extractor;

interface TrackingExtractorInterface
{
    /**
     * @param mixed $object
     */
    public function supports($object): bool;

    /**
     * @param mixed $object
     * @param array $data
     */
    public function updateMetadata($object, $data = []): array;
}
