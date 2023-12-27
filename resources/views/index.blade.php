@extends('homelayout')

@section('head')
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js"></script>
    <!-- Glightbox -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lora&display=swap');

        section {
            color: #021e43;
            padding: 50px 0;
        }

        /* Hero section */
        .hero {
            width: 100%;
            background-size: cover;
            position: relative;
            min-height: 60vh;
            padding: 150px 0 60px 0;
        }

        .hero h2 {
            font-size: 64px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #0c3261;
        }

        .hero p {
            font-weight: 500;
            margin-bottom: 30px;
        }

        .hero .btn-start-order {
            font-weight: 500;
            font-size: 15px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 12px 34px;
            border-radius: 50px;
            transition: 0.5s;
            color: #000;
            background: #f6c329;
            box-shadow: 0 8px 28px rgba(249, 233, 91, 0.2);
            text-decoration: none;
        }

        .hero .btn-start-order:hover {
            background: rgb(255 232 27);
            box-shadow: 0 8px 28px rgba(249, 233, 91, 0.45);
        }

        @media (max-width: 640px) {
            .hero h2 {
                font-size: 36px;
            }
        }

        /* About section */
        .about .about-img {
            min-height: 700px;
        }

        .about .content ul {
            list-style: none;
            padding: 0;
        }

        .about .content ul li {
            padding: 0 0 8px 26px;
            position: relative;
        }

        .about .content ul i {
            position: absolute;
            font-size: 12px;
            left: 0;
            top: 7px;
        }

        .about .content p:last-child {
            margin-bottom: 0;
        }

        .section-header {
            text-align: center;
            padding-bottom: 30px;
        }

        .section-header p {
            margin: 0;
            font-size: 35px;
            font-weight: bold;
            color: #0c3261;
        }

        .section-header p span {
            color: #ac1820;
        }

        /* Gallery section */
        .gallery {
            overflow: hidden;
        }

        .gallery-slider {
            max-height: 500px;
        }

        .gallery .swiper-slide,
        .gallery .swiper-wrapper {
            height: initial;
        }

        .gallery .swiper-pagination {
            margin-top: 20px;
            position: relative;
        }

        .gallery .swiper-pagination .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background-color: #fbeee0;
            opacity: 1;
        }

        .gallery .swiper-pagination .swiper-pagination-bullet-active {
            background-color: #0c3261;
        }

        .gallery .swiper-slide-active {
            text-align: center;
        }

        @media (min-width: 992px) {
            .gallery .swiper-wrapper {
                padding: 40px 0;
            }

            .gallery .swiper-slide-active {
                border: 5px solid #0c3261;
                padding: 4px;
                background: #fff;
                z-index: 1;
                transform: scale(1.2);
            }
        }

        /* Contact section */
        .contact .info-item {
            background: #fbeee0;
            padding: 30px;
            height: 100%;
            border-radius: 10px;
        }

        .contact .info-item .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            font-size: 24px;
            line-height: 0;
            color: #fff;
            background: #0c376c;
            border-radius: 50%;
            margin-right: 15px;
        }

        .contact .info-item h3 {
            font-size: 20px;
            color: #0c376c;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .contact .info-item p,
        .contact .info-item div {
            padding: 0;
            margin: 0;
            line-height: 24px;
            font-size: 15px;
        }

        /* Preloader */
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            overflow: hidden;
            background: #f4e1cd;
            transition: all 0.6s ease-out;
            width: 100%;
            height: 100vh;
        }

        #preloader:before,
        #preloader:after {
            content: "";
            position: absolute;
            border: 4px solid #ac1820;
            border-radius: 50%;
            animation: animate-preloader 2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }

        #preloader:after {
            animation-delay: -0.5s;
        }

        @keyframes animate-preloader {
            0% {
                width: 10px;
                height: 10px;
                top: calc(50% - 5px);
                left: calc(50% - 5px);
                opacity: 1;
            }

            100% {
                width: 72px;
                height: 72px;
                top: calc(50% - 36px);
                left: calc(50% - 36px);
                opacity: 0;
            }
        }
    </style>
@endsection

@section('body')
    <section id="hero" class="hero d-flex align-items-center section-bg">
        <div class="container">
            <div class="row justify-content-between gy-5">
                <div
                    class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center align-items-center align-items-lg-start text-center text-lg-start">
                    <h2 data-aos="fade-up">Enjoy Your Delicious<br>Pizza Delights</h2>
                    <p data-aos="fade-up" data-aos-delay="100">
                        Indulge in a slice of pizza perfection that will ignite your taste buds and transport you to a realm
                        of cheesy, saucy, and irresistible delight.
                    </p>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        <a href="/menu" class="btn-start-order">Order Now</a>
                    </div>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 text-center text-lg-start">
                    <img src="img/home/pizza.png" class="img-fluid" alt="" data-aos="zoom-out" data-aos-delay="300">
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <div class="container" data-aos="fade-up">
            <div class="section-header">
                <p>Learn More <span>About Us</span></p>
            </div>
            <div class="row gy-4">
                <div class="col-lg-7 position-relative about-img" style="background-image: url(img/home/about.jpg) ;"
                    data-aos="fade-up" data-aos-delay="150">
                </div>
                <div class="col-lg-5 d-flex align-items-end" data-aos="fade-up" data-aos-delay="300">
                    <div class="content ps-0 ps-lg-5">
                        <p class="fst-italic">
                            Welcome to our pizza paradise, where you can savor the heavenly taste of freshly baked pizzas
                            crafted with love and passion.
                        </p>
                        <ul>
                            <li><i class="fas fa-star"></i> Source only the finest ingredients, ensuring each bite
                                bursts with exceptional flavors.</li>
                            <li><i class="fas fa-star"></i> Skilled pizza artisans meticulously prepare each pizza
                                with precision and care.</li>
                            <li><i class="fas fa-star"></i> Chefs infuse their creations with passion and
                                creativity, pushing the boundaries of traditional pizza-making.</li>
                        </ul>
                        <p>
                            Whether you're a cheese lover, a pepperoni enthusiast, or an adventurous pizza connoisseur,
                            we've got the perfect slice of delight waiting just for you. Step into our pizzeria and embark
                            on a journey of unforgettable flavors. Your taste buds will thank you.
                        </p>

                        <div class="position-relative mt-4">
                            <img src="img/home/about-2.jpg" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="gallery section-bg">
        <div class="container" data-aos="fade-up">
            <div class="section-header">
                <p>Check <span>Our Gallery</span></p>
            </div>
            <div class="gallery-slider swiper">
                <div class="swiper-wrapper align-items-center">
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-1.jpg"><img src="img/home/gallery-1.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-2.jpg"><img src="img/home/gallery-2.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-3.jpg"><img src="img/home/gallery-3.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-4.jpg"><img src="img/home/gallery-4.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-5.jpg"><img src="img/home/gallery-5.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-6.jpg"><img src="img/home/gallery-6.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-7.jpg"><img src="img/home/gallery-7.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                    <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                            href="img/home/gallery-8.jpg"><img src="img/home/gallery-8.jpg" class="img-fluid"
                                alt=""></a>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container" data-aos="fade-up">
            <div class="section-header">
                <p>Need Help? <span>Contact Us</span></p>
            </div>
            <div class="mb-3">
                <iframe style="border:0; width: 100%; height: 350px;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13439.864369979146!2d-97.39052956895824!3d32.633726421083715!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864e6d7319396151%3A0x9e300ae2a41d6127!2sPerfecto%20Pizza!5e0!3m2!1sen!2smy!4v1687172743207!5m2!1sen!2smy"
                    frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="info-item d-flex align-items-center">
                        <i class="icon far fa-map flex-shrink-0"></i>
                        <div>
                            <h3>Our Address</h3>
                            <p>3651 Sycamore School Rd Ste 100, Fort Worth, TX 76133, United States</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item d-flex align-items-center">
                        <i class="icon far fa-clock flex-shrink-0"></i>
                        <div>
                            <h3>Opening Hours</h3>
                            <div><strong>Mon - Sat:</strong> 9am - 11pm;
                                <strong>Sunday:</strong> Closed
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item d-flex align-items-center">
                        <i class="icon fas fa-voicemail flex-shrink-0"></i>
                        <div>
                            <h3>Call Us</h3>
                            <p>+1 6822 2407 76</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item d-flex align-items-center">
                        <i class="icon far fa-envelope flex-shrink-0"></i>
                        <div>
                            <h3>Email Us</h3>
                            <p>perfectopizzas@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="fas fa-arrow-up"></i></a>

    <div id="preloader"></div>

    <script>
        const preloader = document.querySelector('#preloader');
        if (preloader) {
            window.addEventListener('load', () => {
                preloader.remove();
            });
        }

        function aos_init() {
            AOS.init({
                duration: 1000,
                easing: 'ease-in-out',
                once: true,
                mirror: false
            });
        }

        window.addEventListener('load', () => {
            aos_init();
        });

        const glightbox = GLightbox({
            selector: '.glightbox'
        });

        const swiper = new Swiper('.gallery-slider', {
            speed: 300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            slidesPerView: 'auto',
            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
                clickable: true
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                640: {
                    slidesPerView: 3,
                    spaceBetween: 20
                },
                992: {
                    slidesPerView: 5,
                    spaceBetween: 20
                }
            }
        });
    </script>
@endsection
