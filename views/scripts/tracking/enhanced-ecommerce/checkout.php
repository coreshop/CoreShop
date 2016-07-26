<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
?>


<?php foreach($this->products as $prItem) { ?>
    ga('ec:addProduct', <?= \Zend_Json::encode($prItem) ?>);
<?php } ?>

ga('ec:setAction', 'checkout', <?= json_encode($this->actionData) ?>);
ga('send','event','Checkout','Step');
