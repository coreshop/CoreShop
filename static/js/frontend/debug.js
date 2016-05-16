
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

