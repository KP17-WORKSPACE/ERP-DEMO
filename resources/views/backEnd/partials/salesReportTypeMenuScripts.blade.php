<script>
$(document).ready(function () {
    $('.report-submenu-trigger').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $submenu = $(this).next('.dropdown-menu');
        var isShown = $submenu.hasClass('show');

        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');

        if (!isShown) {
            $submenu.addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });

    $(document).on('click', function () {
        $('.report-type-dropdown .dropend .dropdown-menu').removeClass('show');
        $('.report-submenu-trigger').attr('aria-expanded', 'false');
    });

    $('.report-type-dropdown .dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });
});
</script>
