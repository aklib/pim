$.fn.handleTabs = function () {
    if (window.location.hash) {
        // show tab from url hash on load
        $('a[href="' + window.location.hash + '"]').tab('show').click();
       /* $form = $('#listform');
        alert($form.length);*/
    } else {
        // Select first tab
        $('a.nav-link:first').tab('show');
    }
    window.scrollTo(0, 0);
};


$('a[data-toggle="tab"], a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
    let $this = $(this);
    // add hash to form action url
    let $form = $this.parents('form');
    if($form.length > 0){
        $form = $('#listform');
    }
    //alert($form.length);
    if ($form.length > 0) {
        // handle reload
        let action = $form.attr('action');
        if (action) {
            if (action.match('#')) {
                let split = action.split('#');
                split[1] = e.target.hash;
                $form.attr('action', split.join(''));
            } else {
                $form.attr('action', action + e.target.hash);
            }
        }
        // add hash to url
        window.location.hash = e.target.hash;
        window.scrollTo(0, 0);
        // handle form validation
        const $submitButton = $form.find('button[type="submit"]');
        const $tabs = $this.parent().parent();
        $this.removeClass('text-danger');
        $submitButton.on('click', function () {
            $form.find('input:invalid').each(function () {
                let id = $(this).parents('.tab-pane').attr('id');
                let $tab = $tabs.find('a[href="#' + id + '"]');
                if (!$tab.hasClass('active')) {
                    $tab.addClass('text-danger');
                }
            });
        });
    }
    else {
        // add hash to url
        window.location.hash = e.target.hash;
        window.scrollTo(0, 0);
    }
    // handle ajax content loading
    let url = $this.data('url');
    if (url) {
        let containerId = $this.attr('href');
        if (!$(containerId).hasClass('loaded')) {
            $(containerId).load(url, function () {
                $(containerId).addClass('loaded');
            });
        }

    }
});

$(document).ready(function () {
    $.fn.handleTabs();
});

