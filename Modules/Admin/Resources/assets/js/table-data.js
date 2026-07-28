
"use strict";

$(function (e) {

	// Basic Data Table
	$('#basic-datatable').DataTable({
		language: {
            "searchPlaceholder": 'جست و جو...',
            "sProcessing": "درحال پردازش...",
            "sLengthMenu": "نمایش محتویات _MENU_",
            "sZeroRecords": "موردی یافت نشد",
            "sInfo": "نمایش _START_ تا _END_ از مجموع _TOTAL_ مورد",
            "sInfoEmpty": "خالی",
            "sInfoFiltered": "(فیلتر شده از مجموع _MAX_ مورد)",
            "sInfoPostFix": "",
            "sSearch": "جستجو:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "ابتدا",
                "sPrevious": "قبلی",
                "sNext": "بعدی",
                "sLast": "انتها"
            }
        }
	});

	// Basic Data Table
	$('#responsive-datatable').DataTable({
		responsive: true,
		language: {
            "searchPlaceholder": 'جست و جو...',
            "sProcessing": "درحال پردازش...",
            "sLengthMenu": "نمایش محتویات _MENU_",
            "sZeroRecords": "موردی یافت نشد",
            "sInfo": "نمایش _START_ تا _END_ از مجموع _TOTAL_ مورد",
            "sInfoEmpty": "خالی",
            "sInfoFiltered": "(فیلتر شده از مجموع _MAX_ مورد)",
            "sInfoPostFix": "",
            "sSearch": "جستجو:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "ابتدا",
                "sPrevious": "قبلی",
                "sNext": "بعدی",
                "sLast": "انتها"
            }
        }
	});

	// File-Export Data Table
	var table = $('#file-datatable').DataTable({
		buttons: ['copy', 'excel', 'pdf', 'colvis'],
		responsive: true,
		language: {
            "searchPlaceholder": 'جست و جو...',
            "sProcessing": "درحال پردازش...",
            "sLengthMenu": "نمایش محتویات _MENU_",
            "sZeroRecords": "موردی یافت نشد",
            "sInfo": "نمایش _START_ تا _END_ از مجموع _TOTAL_ مورد",
            "sInfoEmpty": "خالی",
            "sInfoFiltered": "(فیلتر شده از مجموع _MAX_ مورد)",
            "sInfoPostFix": "",
            "sSearch": "جستجو:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "ابتدا",
                "sPrevious": "قبلی",
                "sNext": "بعدی",
                "sLast": "انتها"
            }
        }
	});
	table.buttons().container()
		.appendTo('#file-datatable_wrapper .col-md-6:eq(0)');

	// Delete Data Table
	var table = $('#delete-datatable').DataTable({
		language: {
            "searchPlaceholder": 'جست و جو...',
            "sProcessing": "درحال پردازش...",
            "sLengthMenu": "نمایش محتویات _MENU_",
            "sZeroRecords": "موردی یافت نشد",
            "sInfo": "نمایش _START_ تا _END_ از مجموع _TOTAL_ مورد",
            "sInfoEmpty": "خالی",
            "sInfoFiltered": "(فیلتر شده از مجموع _MAX_ مورد)",
            "sInfoPostFix": "",
            "sSearch": "جستجو:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "ابتدا",
                "sPrevious": "قبلی",
                "sNext": "بعدی",
                "sLast": "انتها"
            }
        }
	});
	$('#delete-datatable tbody').on('click', 'tr', function () {
		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected');
		}
		else {
			table.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
		}
	});
	$('#button').on("click", function () {
		table.row('.selected').remove().draw(false);
	});

	$('#example3').DataTable( {
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return 'Details for '+data[0]+' '+data[1];
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }
    } );
    $('#example2').DataTable({
		responsive: true,
		language: {
            "searchPlaceholder": 'جست و جو...',
            "sProcessing": "درحال پردازش...",
            "sLengthMenu": "نمایش محتویات _MENU_",
            "sZeroRecords": "موردی یافت نشد",
            "sInfo": "نمایش _START_ تا _END_ از مجموع _TOTAL_ مورد",
            "sInfoEmpty": "خالی",
            "sInfoFiltered": "(فیلتر شده از مجموع _MAX_ مورد)",
            "sInfoPostFix": "",
            "sSearch": "جستجو:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "ابتدا",
                "sPrevious": "قبلی",
                "sNext": "بعدی",
                "sLast": "انتها"
            }
        }
	});

	// Select2
	$('.select2').select2({
		minimumResultsForSearch: Infinity
	});


});
