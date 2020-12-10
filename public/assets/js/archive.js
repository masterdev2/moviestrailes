$(document).ready(function(){
    $('body').off('click', '.btnBlock');
    $('body').on('click', '.btnBlock', function(){
        var ID = $(this).data('id');
        $('.home-content').removeClass('active');
        $('.home-content[data-value="'+ID+'"]').addClass('active');
        $('.hs-item').addClass("acc");
        $(this).parent().parent().parent().removeClass("acc");
        $(this).parent().parent().parent().addClass("focus");
    });

    $('body').off('click', '#panel-close');
    $('body').on('click', '#panel-close', function(){
        $('.home-content').removeClass('active');
    });

    $('body').off('click', '#mobile_menu');
    $('body').on('click', '#mobile_menu', function(){
        $('#header_menu').toggleClass('open');
    });
});

