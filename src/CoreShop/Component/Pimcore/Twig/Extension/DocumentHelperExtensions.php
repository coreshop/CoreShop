<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\Document;

final class DocumentHelperExtensions extends \Twig_Extension
{
    public function getTests()
    {
        return [
            new \Twig_Test('document', function ($object) {
                return is_object($object) && $object instanceof Document;
            }),
            new \Twig_Test('document_email', function ($object) {
                return is_object($object) && $object instanceof Document\Email;
            }),
            new \Twig_Test('document_folder', function ($object) {
                return is_object($object) && $object instanceof Document\Folder;
            }),
            new \Twig_Test('document_hardlink', function ($object) {
                return is_object($object) && $object instanceof Document\Hardlink;
            }),
            new \Twig_Test('document_newsletter', function ($object) {
                return is_object($object) && $object instanceof Document\Newsletter;
            }),
            new \Twig_Test('document_page', function ($object) {
                return is_object($object) && $object instanceof Document\Page;
            }),
            new \Twig_Test('document_link', function ($object) {
                return is_object($object) && $object instanceof Document\Link;
            }),
            new \Twig_Test('document_page_snippet', function ($object) {
                return is_object($object) && $object instanceof Document\PageSnippet;
            }),
            new \Twig_Test('document_print', function ($object) {
                return is_object($object) && $object instanceof Document\PrintAbstract;
            }),
            new \Twig_Test('document_print_container', function ($object) {
                return is_object($object) && $object instanceof Document\Printcontainer;
            }),
            new \Twig_Test('document_print_page', function ($object) {
                return is_object($object) && $object instanceof Document\Printpage;
            }),
            new \Twig_Test('document_snippet', function ($object) {
                return is_object($object) && $object instanceof Document\Snippet;
            }),
        ];
    }
}
