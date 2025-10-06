import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

window.addEventListener("load", () => {
	setTimeout(() => {
		if (document.querySelector(".js-similar-posts-swiper")) {
			const swiper = new Swiper('.js-similar-posts-swiper', {
				modules: [Navigation, Pagination],
				updateOnWindowResize: true,
				slidesPerView: 1.1,
				spaceBetween: 40,
				navigation: {
					nextEl: '.js-similar-posts-swiper-button-next',
					prevEl: '.js-similar-posts-swiper-button-prev'
				},
				pagination: {
					el: '.js-similar-posts-swiper-pagination'
				},
				breakpoints: {
					0: {
						slidesPerView: 1.1,
						spaceBetween: 16
					},
					640: {
						slidesPerView: 2.2,
						spaceBetween: 16
					},
					1024: {
						slidesPerView: 3.2,
						spaceBetween: 32
					},
					1280: {
						slidesPerView: 4.2,
						spaceBetween: 32
					}
				}
			});

			setTimeout(() => {
				const swiperEl = document.querySelector('.js-similar-posts-swiper');
				const wrapperEl = swiperEl.querySelector('.swiper-wrapper');
				swiperEl.style.height = wrapperEl.offsetHeight + 'px';
			}, 100);

			if (Array.isArray(swiper)) {
				swiper.forEach(swiperInstance => {
					if (swiperInstance.isLocked) {
						swiperInstance.el.parentNode.querySelector('.js-similar-posts-swiper-button-prev').style.display = 'none';
						swiperInstance.el.parentNode.querySelector('.js-similar-posts-swiper-button-next').style.display = 'none';
					}
				});
			} else {
				if (swiper.isLocked) {
					swiper.el.parentNode.querySelector('.js-similar-posts-swiper-button-prev').style.display = 'none';
					swiper.el.parentNode.querySelector('.js-similar-posts-swiper-button-next').style.display = 'none';
				}
			}
		}
	}, 100);
});
