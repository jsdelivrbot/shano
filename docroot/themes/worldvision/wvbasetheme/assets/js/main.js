(function ($) {
    $(document).ready(function () {

        // Only in production env
        if (window.location.href.search("localhost") == -1) {

            // Prevent from entering the context-menu on images
            // =============================
            $('img').bind('contextmenu', function (e) {
                return false;
            });

            // Deactivate image dragging
            // =============================
            $('img').on('dragstart', function (e) {
                e.preventDefault();
            });
        }
    });
})(jQuery);