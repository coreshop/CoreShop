/**
 * CoreShop
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

(function () {

    $(document).on('click', '.coreshop-debug-panel-heading .coreshop-debug-clickable', function (e) {
        var $this = $(this);
        if (!$this.hasClass('coreshop-debug-panel-collapsed')) {
            $this.parents('.coreshop-debug-panel').find('.coreshop-debug-panel-body').slideUp();
            $this.addClass('coreshop-debug-panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        } else {
            $this.parents('.coreshop-debug-panel').find('.coreshop-debug-panel-body').slideDown();
            $this.removeClass('coreshop-debug-panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        }
    });
})();

