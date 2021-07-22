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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;

interface EventDispatcherInterface
{
    /**
     * @param string            $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request           $request
     */
    public function dispatch($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string            $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request           $request
     */
    public function dispatchPreEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string            $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request           $request
     */
    public function dispatchPostEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);

    /**
     * @param string            $eventName
     * @param MetadataInterface $metadata
     * @param ResourceInterface $resource
     * @param Request           $request
     */
    public function dispatchInitializeEvent($eventName, MetadataInterface $metadata, ResourceInterface $resource, Request $request);
}
