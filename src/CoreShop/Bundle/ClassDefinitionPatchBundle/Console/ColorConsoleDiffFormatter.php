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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ClassDefinitionPatchBundle\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\String\UnicodeString;

class ColorConsoleDiffFormatter
{
    /**
     * @var string
     *
     * @see https://regex101.com/r/ovLMDF/1
     */
    private const PLUS_START_REGEX = '#^(\+.*)#';

    /**
     * @var string
     *
     * @see https://regex101.com/r/xwywpa/1
     */
    private const MINUT_START_REGEX = '#^(\-.*)#';

    /**
     * @var string
     *
     * @see https://regex101.com/r/CMlwa8/1
     */
    private const AT_START_REGEX = '#^(@.*)#';

    /**
     * @var string
     *
     * @see https://regex101.com/r/qduj2O/1
     */
    private const NEWLINES_REGEX = "#\n\r|\n#";

    private string $template;

    public function __construct(
        ) {
        $this->template = sprintf(
            '<comment>    ---------- begin diff ----------</comment>%s%%s%s<comment>    ----------- end diff -----------</comment>' . \PHP_EOL,
            \PHP_EOL,
            \PHP_EOL,
        );
    }

    public function format(string $diff): string
    {
        return $this->formatWithTemplate($diff, $this->template);
    }

    private function formatWithTemplate(string $diff, string $template): string
    {
        $escapedDiff = OutputFormatter::escape(rtrim($diff));

        $escapedDiffLines = array_map(
            static function (UnicodeString $string) {
                return $string->toString();
            },
            (new UnicodeString($escapedDiff))->split(self::NEWLINES_REGEX),
        );

        // remove description of added + remove; obvious on diffs
        foreach ($escapedDiffLines as $key => $escapedDiffLine) {
            if ($escapedDiffLine === '--- Original') {
                unset($escapedDiffLines[$key]);
            }

            if ($escapedDiffLine === '+++ New') {
                unset($escapedDiffLines[$key]);
            }
        }

        $coloredLines = array_map(function (string $string): string {
            $string = $this->makePlusLinesGreen($string);
            $string = $this->makeMinusLinesRed($string);
            $string = $this->makeAtNoteCyan($string);

            if ($string === ' ') {
                return '';
            }

            return $string;
        }, $escapedDiffLines);

        return sprintf($template, implode(\PHP_EOL, $coloredLines));
    }

    private function makePlusLinesGreen(string $string): string
    {
        return (new UnicodeString($string))->replace(self::PLUS_START_REGEX, '<fg=green>$1</fg=green>')->toString();
    }

    private function makeMinusLinesRed(string $string): string
    {
        return (new UnicodeString($string))->replace(self::MINUT_START_REGEX, '<fg=red>$1</fg=red>')->toString();
    }

    private function makeAtNoteCyan(string $string): string
    {
        return (new UnicodeString($string))->replace(self::AT_START_REGEX, '<fg=cyan>$1</fg=cyan>')->toString();
    }
}
