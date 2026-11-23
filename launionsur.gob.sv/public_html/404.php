<?php $head_title="404 || erepair || erepair PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "404";
require_once('parts/page-title.php');
?>

        <!--Error Page Start-->
        <section class="error-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="error-page__inner">
                            <div class="error-page__title-box">
                                <h2 class="error-page__title">404</h2>
                            </div>
                            <h3 class="error-page__tagline">Lo siento no podemos encontrar esta pagina!</h3>
                            <p class="error-page__text">La pagina que estas visualizando no existe.</p>
                            <form class="error-page__form">
                                <div class="error-page__form-input">
                                    <input type="search" placeholder="Search here">
                                    <button type="submit"><i class="icon-search-interface-symbol"></i></button>
                                </div>
                            </form>
                            <a href="index.php" class="thm-btn error-page__btn">Regresar al Inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Error Page End-->

        <!--CTA One Start-->
        <section class="cta-one">
            <div class="container">
                <div class="cta-one__inner">
                    <div class="cta-one__img">
                        <img src="assets/images/resources/cta-one-img-1.png" alt="">
                    </div>
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box">
                            <span class="section-title__tagline">Get To Know Us</span>
                        </div>
                        <div class="section-title__title-box sec-title-animation animation-style2">
                            <h2 class="section-title__title title-animation">Save Money With Specials<br> Discount Offer
                            </h2>
                        </div>
                    </div>
                    <p class="cta-one__text">Pleasures to secure other greater pleasures, or else he endures pains<br>
                        to avoid worse pains to the selection circumstances</p>
                    <div class="cta-one__btn-and-call-box">
                        <div class="cta-one__btn-box">
                            <a href="contact.php" class="cta-one__btn thm-btn">Get Appointment</a>
                        </div>
                        <div class="cta-one__call-box">
                            <div class="icon">
                                <span class="icon-call"></span>
                            </div>
                            <div class="content">
                                <p>Make a call</p>
                                <h4><a href="tel:00554755242">00 55 475 5242</a></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--CTA One End-->

        <?php require_once('parts/footer/footer.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 