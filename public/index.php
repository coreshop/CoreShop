<?php
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
 */

use Pimcore\Bootstrap;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;

//use runtime
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

Bootstrap::setProjectRoot();

return function (Request $request, array $context) {

    // set current request as property on tool as there's no
    // request stack available yet
    Tool::setCurrentRequest($request);

    Bootstrap::bootstrap();
    $kernel = Bootstrap::kernel();

    // reset current request - will be read from request stack from now on
    Tool::setCurrentRequest(null);

    return $kernel;
};

