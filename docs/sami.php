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
use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('vendor')
    ->exclude('tests')
    ->in('../')
;

$xml = simplexml_load_string(file_get_contents('../plugin.xml'));

return new Sami($iterator, [
    'build_dir' => __DIR__.'/build',
    'cache_dir' => __DIR__.'/cache',
    'title' => $xml->plugin->pluginNiceName.' API (Build: '.$xml->plugin->pluginRevision.')',
]);
