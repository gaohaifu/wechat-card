(function(_w) {

    var w = _w;

    //main namespace
    G = {};
    G.Config = {

    };


    G.Fun = {
        init: function() {
            //显示二级导航
            $("#bs-example-navbar-collapse-1 .navbar-nav li.dropdown").mouseover(function() {
                $(this).addClass("open");
            }).mouseout(function() {
                $(this).removeClass("open");
            });

            var carouselSwiper = new Swiper(".carouselSwiper", {
                pagination: {
                    el: ".swiper-pagination",
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            var productSwiper = new Swiper(".productSwiper", {
                effect: "coverflow",
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: "auto",
                initialSlide: 0,
                coverflowEffect: {
                    rotate: 50,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: true,
                },
                pagination: {
                    el: ".swiper-pagination",
                },
            });

            var newsSwiper = new Swiper(".newsSwiper", {
                slidesPerView: 3,
                spaceBetween: 30,
                autoplay: {
                  delay: 2500,
                  disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    320: { //当屏幕宽度大于等于320
                        slidesPerView: 2,
                        spaceBetween: 10
                    },
                    768: { //当屏幕宽度大于等于768 
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                }
            });

            $(window).scroll(function(e) {
                if ($(window).scrollTop() > 100) {
                    $(".gotop").fadeIn(500).css("display", "block");
                } else {
                    $(".gotop").fadeOut(500).css("display", "none");
                }
            });

            $(".gotop").click(function(e) {
                $('body,html').animate({ scrollTop: 0 }, 500);
                return false;
            });

            //提交反馈
            if ($.cookie('SET_GUESTBOOK_CLOSE') == 1)
            {
                $('.xccms-guestbook-box .body').hide();
                $('.xccms-guestbook-box').addClass('closed');
                $('.xccms-guestbook-box').find('use').attr('href', '#rectangle-one');
            }
            $('.xccms-guestbook-box .header').click(function(){
                var that = $(this).find('a');
                if ($('.xccms-guestbook-box .body').is(':hidden'))
                {
                    that.find('use').attr('href', '#minus');
                    $('.xccms-guestbook-box .body').show();
                    $('.xccms-guestbook-box').removeClass('closed');
                    $.cookie('SET_GUESTBOOK_CLOSE', 0, { expires: 1, path: '/' });
                }
                else
                {
                    that.find('use').attr('href', '#rectangle-one');
                    $('.xccms-guestbook-box .body').hide();
                    $('.xccms-guestbook-box').addClass('closed');
                    $.cookie('SET_GUESTBOOK_CLOSE', 1, { expires: 1, path: '/' });
                }
            });

            $('.xccms-guestbook-box form').submit(function(e){
                e.preventDefault();
                var that = $(this);
                $.post(
                    that.attr('action'),
                    {
                        realname: $('#realname').val(),
                        tel: $('#tel').val(),
                        email: $('#email').val(),
                        content: $('#content').val(),
                        captcha: $('#captcha').val(),
                        page_code: $('#page_code').val(),
                        page_id: $('#page_id').val()
                    },
                    function(d){
                        console.log(d);
                        if (d['code'] == 1)
                        {
                            $('#realname, #tel, #email, #content, #captcha').val('');
                        }
                        $('#captcha-image').click();
                        $('.xccms-guestbook-box .body .result-box').show();
                        $('.xccms-guestbook-box .body .result-box div span').text(d['msg']);

                        setTimeout(function(){
                            $('.xccms-guestbook-box .body .result-box').hide();
                        }, 2000);
                    }
                    ,'json'
                );
            })

        }
    };

    $(function() {
        new WOW().init();
        $('.counter').countUp();
        G.Fun.init();
    });
})();