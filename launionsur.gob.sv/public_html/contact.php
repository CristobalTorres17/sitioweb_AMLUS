<?php $head_title="Contáctenos || Alcaldia || Unión Sur PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "Contáctenos";
require_once('parts/page-title.php');
?>
        <!--Contact Two Start-->
        <section class="contact-two">
            <div class="container">
                <div class="row">
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-call"></span>
                            </div>
                            <p>Contáctenos</p>
                            <h3><a href="tel:+503 26097000">+503 26097000</a></h3>
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-envelope"></span>
                            </div>
                            <p>Correo</p>
                            <h3><a href="mailto:@launionsur.gob.sv">@launionsur.gob.sv</a></h3>
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-location"></span>
                            </div>
                            <p>Ubicación</p>
                            <h3>Avenida Gral Cabañas, La Unión</h3>
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                </div>
            </div>
        </section>
        <!--Contact Two End-->

        <!--Contact Three Start-->
        <section class="contact-three">
            <div class="container">
                <div class="contact-three__inner">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="contact-three__left">
                                <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3902.078682315548!2d-87.84838082595573!3d13.336536986277413!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f66113fc50cc10d%3A0x434a7df91dd7c325!2sAlcald%C3%ADa%20Municipal%20de%20La%20Uni%C3%B3n!5e0!3m2!1ses!2ssv!4v1710000000000!5m2!1ses!2ssv" 
                                class="google-map__one" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="contact-three__right">
                                <h3 class="contact-three__form-title"> Distrito La Unión Sur</h3>
                                <form id="contact-form" class="contact-form-validated contact-three__form"
                                    action="assets/mail.php" method="POST">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="contact-three__input-box">
                                                <input type="text" name="name" placeholder="Nombre " required="">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="contact-three__input-box">
                                                <input type="text" name="name" placeholder="Apellido" required="">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="contact-three__input-box">
                                                <input type="email" name="email" placeholder="Correo" required="">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="contact-three__input-box">
                                                <input type="number" placeholder="Celular" name="number">
                                            </div>
                                        </div>
                                      
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="contact-three__input-box text-message-box">
                                                <textarea name="message" placeholder="Mensaje"></textarea>
                                            </div>
                                            <div class="contact-three__btn-box">
                                                <button type="submit" class="thm-btn contact-three__btn"
                                                    data-loading-text="Please wait...">
                                                    Enviar Mensaje
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <p class="ajax-response mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <br/><br/><br/> <br/><br/><br/>
        <br/><br/><br/><br/><br/><br/><br/>
        <!--Contact Three End-->


        <?php require_once('parts/home/cta.php'); ?>
        <?php require_once('parts/footer/footer.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 
       