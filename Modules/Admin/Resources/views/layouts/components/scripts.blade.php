<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-long-arrow-up"></i></a>

<!-- JQUERY JS -->
<script src="{{ admin_asset('plugins/jquery/jquery.min.js') }}"></script>

<!-- BOOTSTRAP JS -->
<script src="{{ admin_asset('plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ admin_asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- SIDE-MENU JS -->
<script src="{{ admin_asset('plugins/sidemenu/sidemenu.js') }}"></script>
<!-- Perfect SCROLLBAR JS-->
<script src="{{ admin_asset('plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ admin_asset('plugins/p-scroll/pscroll.js') }}"></script>
<script src="{{ admin_asset('js/jalalidatepicker.min.js') }}"></script>

<!-- STICKY JS -->
<script src="{{ admin_asset('js/sticky.js') }}"></script>

@yield('scripts')

<!-- COLOR THEME JS -->
<script src="{{ admin_asset('js/themeColors.js') }}"></script>
<script src="{{ admin_asset('js/persian-date.min.js') }}"></script>
<script src="{{ admin_asset('js/persian-datepicker.min.js') }}"></script>

<!-- CUSTOM JS -->
<script src="{{ admin_asset('js/custom.js') }}"></script>
<script>
    (function($) {

        $(function() {
            $('.persian-number').on('input', function() {
                var inputValue = $(this).val();
                var latinValue = convertToLatin(inputValue);
                $(this).val(latinValue);
            });

            function convertToLatin(inputValue) {
                var persianDigits = '۰۱۲۳۴۵۶۷۸۹';
                var arabicDigits = '٠١٢٣٤٥٦٧٨٩';
                var latinDigits = '0123456789';

                var convertedValue = '';
                for (var i = 0; i < inputValue.length; i++) {
                    var char = inputValue[i];
                    var index = persianDigits.indexOf(char);
                    if (index !== -1) {
                        convertedValue += latinDigits[index];
                    } else {
                        index = arabicDigits.indexOf(char);
                        if (index !== -1) {
                            convertedValue += latinDigits[index];
                        } else {
                            convertedValue += char;
                        }
                    }
                }
                return convertedValue;
            }

        });

    })(jQuery)
</script>
<script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('success', function(param) {
            showSwalSuccess(param.message);
        });
        Livewire.on('error', function(param) {
            swalError(param.message);
        });
    })
</script>
