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

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\Document;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class DocumentHelperExtensions extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('document', static function ($object) {
                return is_object($object) && $object instanceof Document;
            }),
            new TwigTest('document_email', static function ($object) {
                return is_object($object) && $object instanceof Document\Email;
            }),
            new TwigTest('document_folder', static function ($object) {
                return is_object($object) && $object instanceof Document\Folder;
            }),
            new TwigTest('document_hardlink', static function ($object) {
                return is_object($object) && $object instanceof Document\Hardlink;
            }),
            new TwigTest('document_newsletter', static function ($object) {
                return is_object($object) && $object instanceof Document\Newsletter;
            }),
            new TwigTest('document_page', static function ($object) {
                return is_object($object) && $object instanceof Document\Page;
            }),
            new TwigTest('document_link', static function ($object) {
                return is_object($object) && $object instanceof Document\Link;
            }),
            new TwigTest('document_page_snippet', static function ($object) {
                return is_object($object) && $object instanceof Document\PageSnippet;
            }),
            new TwigTest('document_print',static  function ($object) {
                return is_object($object) && $object instanceof Document\PrintAbstract;
            }),
            new TwigTest('document_print_container', static function ($object) {
                return is_object($object) && $object instanceof Document\Printcontainer;
            }),
            new TwigTest('document_print_page', static function ($object) {
                return is_object($object) && $object instanceof Document\Printpage;
            }),
            new TwigTest('document_snippet', static function ($object) {
                return is_object($object) && $object instanceof Document\Snippet;
            }),
        ];
    }
}
