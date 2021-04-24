$.fn.ajaxModal = function(callback) {

    let $activeLink = null;
    let spinner = '<div class="spinner spinner-primary spinner-lg mr-15"></div>';
    $("#ajax-modal").on("show.bs.modal", function(e) {
        $(this).html(spinner);
        $activeLink = $(e.relatedTarget);
        $activeLink.parents('tr:first').addClass('success');
        $(this).load($activeLink.attr("href"));
    });
    //force reload content
    $('body').on('hidden.bs.modal', function() {
        $('#ajax-modal').removeData('bs.modal').html(spinner);
        if($activeLink){
            $activeLink.parents('tr:first').removeClass('success');
            $activeLink = null;
        }
        //  fix tooltips bug
        $('.tooltips').tooltip('hide');
    })
};
$(document).ready(function() {
    $.fn.ajaxModal();
});






