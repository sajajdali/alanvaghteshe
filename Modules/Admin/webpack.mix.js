const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
// JS MIX //
mix
    .js('Resources/assets/js/address-editable.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/apexcharts.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/reply.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/blog-edit.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/bootstrap-editable.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/calendar.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/carousel.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/chart.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/chat.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/checkout.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/client-create.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/colorpicker.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/custom.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/pusher.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/custom1.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/echarts.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/flot.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/form-editor2.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/formelementadvnced.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/form-elements.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/form-layouts.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/form-validation.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/form-wizard.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/fullcalendar.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/index1.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/index.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/invoice-create.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/invoice-edit.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/invoice-timelog.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/jvectormap.js', '../../public/assets/admin/js')
    .copyDirectory('Resources/assets/js/landing.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/mail-settings.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/map-leafleft.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/mapelmaps.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/morris.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/nvd3.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/products.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/profile.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/progress.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/project-details.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/project-list.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/projects-edit.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/projects-new.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/projects.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/rangeslider.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/select2.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/sticky.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/summernote.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/table-data.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/table-editable.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/task-create.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/task-edit.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/tasks-list.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/themeColors.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/ticket-details.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/timline.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/tooltip&popover.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/TypeHead.js', '../../public/assets/admin/js')
    .js('Resources/assets/js/userlist.js', '../../public/assets/admin/js')
    .postCss('Resources/assets/css/animated.css', '../../public/assets/admin/css')
    .sass('Resources/assets/css/skin-modes.scss', '../../public/assets/admin/css')
    .sass('Resources/assets/scss/style.scss', '../../public/assets/admin/css')
    .copyDirectory('Resources/assets/images', '../../public/assets/admin/images')
    .copyDirectory('Resources/assets/plugins', '../../public/assets/admin/plugins')
    .copyDirectory('Resources/assets/switcher', '../../public/assets/admin/switcher')
    .copyDirectory('Resources/assets/fonts', '../../public/assets/admin/fonts')
    .options({
        processCssUrls: false
    });

// mix.browserSync('http://127.0.0.1:8000');
