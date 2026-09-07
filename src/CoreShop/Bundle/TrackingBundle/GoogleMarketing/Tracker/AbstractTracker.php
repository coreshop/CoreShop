<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 * Originally derived from pimcore/google-marketing-bundle (POCL).
 */

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Tracker;

use CoreShop\Bundle\TrackingBundle\GoogleMarketing\Code\CodeCollector;
use CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId\SiteId;
use CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId\SiteIdProvider;

abstract class AbstractTracker implements TrackerInterface
{
    private SiteIdProvider $siteIdProvider;

    private ?CodeCollector $codeCollector = null;

    public function __construct(SiteIdProvider $siteIdProvider)
    {
        $this->siteIdProvider = $siteIdProvider;
    }

    public function generateCode(?SiteId $siteId = null): ?string
    {
        if (null === $siteId) {
            $siteId = $this->siteIdProvider->getForRequest();
        }

        return $this->buildCode($siteId);
    }

    abstract protected function buildCode(SiteId $siteId): ?string;

    public function addCodePart(string $code, ?string $block = null, bool $prepend = false, ?SiteId $siteId = null): void
    {
        $action = $prepend ? CodeCollector::ACTION_PREPEND : CodeCollector::ACTION_APPEND;

        $this->getCodeCollector()->addCodePart($code, $block, $action, $siteId);
    }

    protected function getCodeCollector(): CodeCollector
    {
        if (null === $this->codeCollector) {
            $this->codeCollector = $this->buildCodeCollector();
        }

        return $this->codeCollector;
    }

    abstract protected function buildCodeCollector(): CodeCollector;
}
