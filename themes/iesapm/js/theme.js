jQuery(document).ready(function ($) {

    /*=================================================
    CONFIGURACAO HEADER STICKY
    =================================================*/
    $(window).scroll(function () {
        if ($(window).scrollTop() > 100) {
            $('.l-header-site').addClass('sticky');
        }
        else {
            $('.l-header-site').removeClass('sticky');
        }
    });


    /*=================================================
    CONFIGURAÇÃO MENU RESPONSIVO
    ===================================================*/
    $('.js-nav-toggle').on('click', function () {
        $('.js-nav-menu').toggleClass('show-menu');
        $('body').toggleClass('overflow-hidden');
    });

    $('.nav-menu-principal li.menu-item-has-children > a').on('click', function () {
        $(this).toggleClass('ativo');
        $(this).next(".sub-menu").toggleClass('aberto');
    });

    var body = $('body');
});
