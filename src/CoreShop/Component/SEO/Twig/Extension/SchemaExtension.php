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

namespace CoreShop\Component\SEO\Twig\Extension;

use CoreShop\Component\SEO\SEOSchemaPresentationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SchemaExtension extends AbstractExtension
{
    protected $schemaPresentation;

    public function __construct(SEOSchemaPresentationInterface $schemaPresentation)
    {
        $this->schemaPresentation = $schemaPresentation;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_schema', [$this, 'schema'], ['is_safe' => ['html']]),
        ];
    }

    public function schema($element)
    {
        return $this->schemaPresentation->createSchema($element)->toScript();
    }
}
