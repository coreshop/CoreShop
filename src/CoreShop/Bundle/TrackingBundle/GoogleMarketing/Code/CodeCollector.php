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

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Code;

use CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId\SiteId;

class CodeCollector
{
    public const string CONFIG_KEY_GLOBAL = '__global';

    public const string ACTION_PREPEND = 'prepend';

    public const string ACTION_APPEND = 'append';

    private string $defaultBlock;

    /**
     * @var array<int, string>
     */
    private array $validBlocks;

    /**
     * @var array<string, array<string, array<string, array<int, string>>>>
     */
    private array $codeParts = [];

    /**
     * @var array<int, string>
     */
    private array $validActions = [
        self::ACTION_PREPEND,
        self::ACTION_APPEND,
    ];

    /**
     * @param array<int, string> $validBlocks
     */
    public function __construct(array $validBlocks, string $defaultBlock)
    {
        if (!in_array($defaultBlock, $validBlocks, true)) {
            throw new \LogicException(sprintf(
                'The default block "%s" must be a part of the valid blocks',
                $defaultBlock,
            ));
        }

        $this->validBlocks = $validBlocks;
        $this->defaultBlock = $defaultBlock;
    }

    public function addCodePart(string $code, ?string $block = null, string $action = self::ACTION_APPEND, ?SiteId $siteId = null): void
    {
        if (!in_array($action, $this->validActions, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid action "%s". Valid actions are: %s',
                $action,
                implode(', ', $this->validActions),
            ));
        }

        $configKey = self::CONFIG_KEY_GLOBAL;
        if (null !== $siteId) {
            $configKey = $siteId->getConfigKey();
        }

        if (null === $block) {
            $block = $this->defaultBlock;
        }

        if (!in_array($block, $this->validBlocks, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid block "%s". Valid values are: %s',
                $block,
                implode(', ', $this->validBlocks),
            ));
        }

        $this->codeParts[$configKey][$block][$action][] = $code;
    }

    public function enrichCodeBlock(SiteId $siteId, CodeBlock $codeBlock, string $block): void
    {
        $this->enrichBlock(self::CONFIG_KEY_GLOBAL, $codeBlock, $block);
        $this->enrichBlock($siteId->getConfigKey(), $codeBlock, $block);
    }

    private function enrichBlock(string $configKey, CodeBlock $codeBlock, string $block): void
    {
        if (!isset($this->codeParts[$configKey])) {
            return;
        }

        $blockParts = $this->codeParts[$configKey][$block] ?? [];
        if ($blockParts === []) {
            return;
        }

        foreach ([self::ACTION_PREPEND, self::ACTION_APPEND] as $position) {
            if (!isset($blockParts[$position])) {
                continue;
            }
            $parts = $blockParts[$position];
            if (self::ACTION_PREPEND === $position) {
                $codeBlock->prepend($parts);
            } else {
                $codeBlock->append($parts);
            }
        }
    }
}
