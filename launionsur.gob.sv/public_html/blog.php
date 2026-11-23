<?php 
$head_title = "Noticias || La Union Sur || Distrito PHP Template";

// Incluye la configuración de la conexión
require_once 'config/config.php';

// Configuración del paginador
$eventosPorPagina = 6; // Número de eventos a mostrar por página
$paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Consulta para obtener el total de eventos
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM eventos");
$stmtCount->execute();
$totalEventos = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalEventos / $eventosPorPagina);

// Calcular el offset para la consulta
$offset = ($paginaActual - 1) * $eventosPorPagina;

// Consulta para obtener los eventos de la página actual
$stmt = $pdo->prepare("SELECT id_evento, nombre, fecha, tipo, listado 
                        FROM eventos 
                        ORDER BY fecha DESC 
                        LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $eventosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$eventos = $stmt->fetchAll();
?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php 
$page_title = "Eventos & Noticias";
require_once('parts/page-title.php'); 
?>

<!--Blog One Start-->
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
              <!-- Se muestran dos imágenes (normal y hover) con la misma fuente en este ejemplo -->
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
    
    <!-- Paginador -->
<!-- Paginador -->
<ul class="styled-pagination pdtop30 clearfix">
  <?php if ($paginaActual > 1): ?>
    <li class="arrow prev">
      <a href="blog.php?page=<?= $paginaActual - 1 ?>">
        <span class="icon-arrow-right left"></span>
      </a>
    </li>
  <?php endif; ?>

  <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
    <li class="<?= ($i == $paginaActual) ? 'active' : '' ?>">
      <a href="blog.php?page=<?= $i ?>"><?= $i ?></a>
    </li>
  <?php endfor; ?>

  <?php if ($paginaActual < $totalPaginas): ?>
    <li class="arrow next">
      <a href="blog.php?page=<?= $paginaActual + 1 ?>">
        <span class="icon-arrow-right right"></span>
      </a>
    </li>
  <?php endif; ?>
</ul>

  </div>
</section>
<!--Blog One End-->

<?php require_once('parts/home/cta.php'); ?>
<?php 
require_once('parts/footer/footer.php'); 
require_once('parts/layout/bottom-layout.php'); 
?>
