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
                @trigger_error(
                    'document 3.0.0 and will be removed in 3.1.0. Use pimcore_document instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document;
            }),
            new TwigTest('document_email', static function ($object) {
                @trigger_error(
                    'document_email 3.0.0 and will be removed in 3.1.0. Use pimcore_document_email instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Email;
            }),
            new TwigTest('document_folder', static function ($object) {
                @trigger_error(
                    'document_folder 3.0.0 and will be removed in 3.1.0. Use pimcore_document_folder instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Folder;
            }),
            new TwigTest('document_hardlink', static function ($object) {
                @trigger_error(
                    'document_hardlink 3.0.0 and will be removed in 3.1.0. Use pimcore_document_hardlink instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Hardlink;
            }),
            new TwigTest('document_newsletter', static function ($object) {
                @trigger_error(
                    'document_newsletter 3.0.0 and will be removed in 3.1.0. Use pimcore_document_newsletter instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Newsletter;
            }),
            new TwigTest('document_page', static function ($object) {
                @trigger_error(
                    'document_page 3.0.0 and will be removed in 3.1.0. Use pimcore_document_page instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Page;
            }),
            new TwigTest('document_link', static function ($object) {
                @trigger_error(
                    'document_link 3.0.0 and will be removed in 3.1.0. Use pimcore_document_link instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Link;
            }),
            new TwigTest('document_page_snippet', static function ($object) {
                @trigger_error(
                    'document_page_snippet 3.0.0 and will be removed in 3.1.0. Use pimcore_document_page_snippet instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\PageSnippet;
            }),
            new TwigTest('document_print',static  function ($object) {
                @trigger_error(
                    'document_print 3.0.0 and will be removed in 3.1.0. Use pimcore_document_print instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\PrintAbstract;
            }),
            new TwigTest('document_print_container', static function ($object) {
                @trigger_error(
                    'document_print_container 3.0.0 and will be removed in 3.1.0. Use pimcore_document_print_container instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Printcontainer;
            }),
            new TwigTest('document_print_page', static function ($object) {
                @trigger_error(
                    'document_print_page 3.0.0 and will be removed in 3.1.0. Use pimcore_document_print_page instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Printpage;
            }),
            new TwigTest('document_snippet', static function ($object) {
                @trigger_error(
                    'document_snippet 3.0.0 and will be removed in 3.1.0. Use pimcore_document_snippet instead.',
                    E_USER_DEPRECATED
                );

                return is_object($object) && $object instanceof Document\Snippet;
            }),
        ];
    }
}
