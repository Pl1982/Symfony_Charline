import Swiper from 'swiper';
import { Autoplay, Pagination } from 'swiper/modules';

import 'swiper/scss';
import 'swiper/scss/pagination';

const swiper = new Swiper('.banner-slider', {
    modules: [Autoplay, Pagination],
    loop: true,
    grabCursor: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    autoplay: {
        disableOnInteraction: true,
        delay: 3500,
    }
});