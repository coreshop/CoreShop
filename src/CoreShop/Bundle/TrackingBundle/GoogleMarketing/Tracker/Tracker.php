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

class Tracker extends AbstractTracker
{
    public const string BLOCK_BEFORE_SCRIPT_TAG = 'beforeScriptTag';

    public const string BLOCK_BEFORE_SCRIPT = 'beforeScript';

    public const string BLOCK_BEFORE_INIT = 'beforeInit';

    public const string BLOCK_BEFORE_TRACK = 'beforeTrack';

    public const string BLOCK_AFTER_TRACK = 'afterTrack';

    public const string BLOCK_AFTER_SCRIPT = 'afterScript';

    public const string BLOCK_AFTER_SCRIPT_TAG = 'afterScriptTag';

    /**
     * @var array<int, string>
     */
    private array $blocks = [
        self::BLOCK_BEFORE_SCRIPT_TAG,
        self::BLOCK_BEFORE_SCRIPT,
        self::BLOCK_BEFORE_INIT,
        self::BLOCK_BEFORE_TRACK,
        self::BLOCK_AFTER_TRACK,
        self::BLOCK_AFTER_SCRIPT,
        self::BLOCK_AFTER_SCRIPT_TAG,
    ];

    protected function buildCodeCollector(): CodeCollector
    {
        return new CodeCollector($this->blocks, self::BLOCK_AFTER_TRACK);
    }

    protected function buildCode(SiteId $siteId): ?string
    {
        return null;
    }
}
