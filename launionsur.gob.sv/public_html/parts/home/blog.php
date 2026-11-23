<section class="blog-one">
    <div class="container">
        <div class="blog-one__top">
            <div class="section-title text-left">
                <div class="section-title__tagline-box">
                    <span class="section-title__tagline">Eventos & Noticias</span>
                </div>
                <div class="section-title__title-box sec-title-animation animation-style2">
                    <h2 class="section-title__title title-animation">Próximos Eventos<br> en la Alcaldía</h2>
                </div>
            </div>
            <div class="blog-one__btn-box">
                <a href="blog.php" class="blog-one__btn thm-btn">Ver todos los eventos</a>
            </div>
        </div>
        <div class="row">
            <?php foreach ($eventos as $evento): ?>
            <!--Blog One Single Start-->
            <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="100ms">
                <div class="blog-one__single">
                    <div class="blog-one__img-box">
                        <div class="blog-one__img">
                            <!-- Imagen normal y de hover (se usa la misma en este ejemplo) -->
                            <img src="<?php echo "http://127.0.0.1:8083/admin/eventos/images/". htmlspecialchars($evento['listado']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                            <img src="<?php echo "http://127.0.0.1:8083/admin/eventos/images/". htmlspecialchars($evento['listado']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                            <a href="blog-details.php?id=<?php echo htmlspecialchars($evento['id_evento']); ?>" class="blog-one__link">
                                <span class="sr-only">Ver Detalle</span>
                            </a>
                        </div>
                        <div class="blog-one__date">
                            <p>
                                <?php 
                                // Formatear la fecha: día y mes
                                echo date('d', strtotime($evento['fecha'])); 
                                echo '<br>';
                                echo date('M', strtotime($evento['fecha']));
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="blog-one__content">
                        <div class="blog-one__user">
                            <p><span class="icon-user"></span> <?php echo htmlspecialchars($evento['tipo']); ?></p>
                        </div>
                        <h3 class="blog-one__title">
                            <a href="blog-details.php?id=<?php echo htmlspecialchars($evento['id_evento']); ?>">
                                <?php echo htmlspecialchars($evento['nombre']); ?>
                            </a>
                        </h3>
                        <a href="blog-details.php?id=<?php echo htmlspecialchars($evento['id_evento']); ?>" class="blog-one__learn-more">
                            Ver Detalle<span class="icon-arrow-right"></span>
                        </a>
                    </div>
                </div>
            </div>
            <!--Blog One Single End-->
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!--Blog One End-->