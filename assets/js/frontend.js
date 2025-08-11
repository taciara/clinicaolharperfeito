jQuery(function($) {
    $(document).ready(function() {

        //SCROLL
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();
            if (scroll >= 50) {
                $("header#header").addClass("nav-fixed");
            } else {
                $("header#header").removeClass("nav-fixed");
            }
        });

        //mascaras input telefone
        if ($(".phone").length > 0) {
            var SPMaskBehavior = function(val) {
                return val.replace(/\D/g, "").length === 11 ?
                    "(00) 00000-0000" :
                    "(00) 0000-00009";
            };

            var spOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                },
            };

            $(".phone").mask(SPMaskBehavior, spOptions);
        }
		
		$('.cnpj').mask('00.000.000/0000-00', {reverse: true});

    });

    // Seletor para o link pai
    const $dropdownLink = $('.nav-item.menu-has-children .nav-link');

    // Seletor para o conteúdo do dropdown
    const $dropdownContent = $('.nav-item .submenu');

    // Variável para rastrear o estado do dropdown
    let isDropdownVisible = false;

    // Adiciona um evento de clique ao link pai
    $dropdownLink.on('click', function(event) {
        // Verifica se a largura da tela é menor que 992px
        if ($(window).width() < 992) {
            // Impede o redirecionamento padrão
            event.preventDefault();

            // Alterna a exibição do dropdown
            isDropdownVisible = !isDropdownVisible;
            $dropdownContent.toggle(isDropdownVisible);
        } else {
            // Se a largura da tela for 992px ou mais, permita a navegação
            const href = $(this).attr('href');
            window.location.href = href;
        }
    });

});


