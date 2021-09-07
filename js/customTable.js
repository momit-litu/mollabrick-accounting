/** ******  table  *********************** **/
$('table input').on('ifChecked', function () {	
    check_state = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('table input').on('ifUnchecked', function () {
    check_state = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var check_state = '';
$('.bulk_action input').on('ifChecked', function () {
    check_state = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    check_state = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    check_state = 'check_all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    check_state = 'uncheck_all';
    countChecked();
});

function countChecked() {
    if (check_state == 'check_all') {
        $(".bulk_action .tableflat").iCheck('check');
    }
    if (check_state == 'uncheck_all') {
        $(".bulk_action .tableflat").iCheck('uncheck');
    }
    var n = $(".bulk_action .tableflat:checked").length-1;
    if (n > 0) {
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(n + " "+record_selected_ln);
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}
/** ******  /table  *********************** **/