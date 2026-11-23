<?php
$head_title = "Blog Details || erepair || erepair PHP Template";

// Incluir la configuración y establecer la conexión a la base de datos
require_once 'config/config.php';

// Recuperar el id del evento desde la URL
$id_evento = $_GET['id'] ?? null;
if (!$id_evento) {
    echo "Evento no encontrado.";
    exit;
}

// Consultar los datos completos del evento seleccionado
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_evento = :id");
$stmt->bindValue(':id', $id_evento, PDO::PARAM_INT);
$stmt->execute();
$evento = $stmt->fetch();
if (!$evento) {
    echo "Evento no encontrado.";
    exit;
}

// Consultar los eventos recientes (últimos 5)
$stmtRecent = $pdo->prepare("SELECT id_evento, nombre, fecha, tipo, listado FROM eventos ORDER BY fecha DESC LIMIT 5");
$stmtRecent->execute();
$recentEvents = $stmtRecent->fetchAll();
?>
<?php require_once('parts/layout/top-layout.php'); ?>
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = $evento['nombre'];
require_once('parts/page-title.php');
?>

<!--Blog Details Start-->
<section class="blog-details">
    <div class="container">
        <div class="row">
            <!-- Contenido principal: detalles del evento -->
            <div class="col-xl-8 col-lg-7">
                <div class="blog-details__left">
                    <div class="blog-details__img">
                        <img src="<?php echo 'http://127.0.0.1:8083/admin/eventos/images/' . htmlspecialchars($evento['listado']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                        <div class="blog-details__date">
                            <p>
                                <?php
                                echo date('d', strtotime($evento['fecha']));
                                echo '<br>';
                                echo date('M', strtotime($evento['fecha']));
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="blog-details__content">
                        <div class="blog-details__user-and-meta">
                            <div class="blog-details__user">
                                <p><span class="icon-user"></span> <?php echo htmlspecialchars($evento['tipo']); ?></p>
                            </div>
                            <ul class="blog-details__meta list-unstyled">
                                <li>
                                    <a href="#"><span class="icon-speech-bubbles"></span>Comments (0)</a>
                                </li>
                                <li>
                                    <a href="#"><span class="icon-clock"></span> <?php echo date('g:i a', strtotime($evento['fecha'])); ?></a>
                                </li>
                            </ul>
                        </div>
                        <h3 class="blog-details__title"><?php echo htmlspecialchars($evento['nombre']); ?></h3>
                        <p class="blog-details__text-1">
                            <?php
                            // Se utiliza 'descripcion' para el contenido; ajusta el nombre de la columna si es necesario.
                            echo nl2br(htmlspecialchars($evento['objetivo']));
                            ?>
                        </p>
                        <!-- Puedes agregar más secciones dinámicas según lo requieras -->

                        <!-- Sección para compartir (opcional) -->
                        <div class="blog-details__share-box">
                            <h3 class="blog-details__share-title">Share :</h3>
                            <div class="blog-details__share">
                                <a href="#"><span class="icon-facebook"></span></a>
                                <a href="#"><span class="icon-xpa"></span></a>
                                <a href="#"><span class="icon-link-in"></span></a>
                                <a href="#"><span class="icon-instagram"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4 col-lg-5">
                <div class="sidebar">
                    <!-- Buscador -->
                    <div class="sidebar__single sidebar__search">
  <form action="#" class="sidebar__search-form" onsubmit="return false;" style="position: relative;">
    <input type="search" id="search-input" placeholder="Buscar noticia..." style="width: 100%;">
    <button type="submit"><i class="icon-search-interface-symbol"></i></button>
    <!-- Loader de búsqueda, inicialmente oculto -->
    <div id="search-loader" style="display:none; margin-top:5px;">
      <i class="fa fa-spinner fa-spin"></i> Buscando...
    </div>
    <!-- Listado de resultados, se posiciona de forma absoluta y tendrá el mismo ancho que el input -->
    <ul id="search-results" class="list-unstyled" style="display:none; position: absolute; z-index: 999; background: #fff; width: 100%; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; margin: 0; padding: 0;"></ul>
  </form>
</div>


                    <!-- Eventos Recientes -->
                    <div class="sidebar__single sidebar__post">
                        <h3 class="sidebar__title">Noticias Recientes</h3>
                        <ul class="sidebar__post-list list-unstyled">
                            <?php foreach ($recentEvents as $recent): ?>
                                <li>
                                    <div class="sidebar__post-image">
                                        <img src="<?php echo 'http://127.0.0.1:8083/admin/eventos/images/' . htmlspecialchars($recent['listado']); ?>" alt="<?php echo htmlspecialchars($recent['nombre']); ?>">
                                    </div>
                                    <div class="sidebar__post-content">
                                        <p class="sidebar__post-date"><?php echo date('d M Y', strtotime($recent['fecha'])); ?></p>
                                        <h3>
                                            <a href="blog-details.php?id=<?php echo htmlspecialchars($recent['id_evento']); ?>">
                                                <?php echo htmlspecialchars($recent['nombre']); ?>
                                            </a>
                                        </h3>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Se han eliminado los tags -->
                </div>
            </div>
        </div>
    </div>
</section>
<!--Blog Details End-->

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
                    <h2 class="section-title__title title-animation">Save Money With Specials<br> Discount Offer</h2>
                </div>
            </div>
            <p class="cta-one__text">
                Pleasures to secure other greater pleasures, or else he endures pains<br>
                to avoid worse pains to the selection circumstances
            </p>
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

<!-- Scripts para el buscador -->
<script>
$(document).ready(function(){
  $("#search-input").on("keyup", function(){
    var query = $(this).val().trim();
    $("#search-results").empty(); // limpiar resultados anteriores

    // Si la consulta está vacía o tiene menos de 3 caracteres, oculta el listado
    if(query.length < 3) {
      $("#search-results").hide();
      $("#search-loader").hide();
      return;
    }
    
    // Mostrar el loader mientras se busca
    $("#search-loader").show();
    
    $.ajax({
      url: "search-event.php", // Endpoint para buscar eventos
      type: "GET",
      data: { query: query },
      dataType: "json",
      success: function(data){
        $("#search-loader").hide();
        $("#search-results").empty();
        if(data.length > 0){
          $.each(data, function(index, eventItem){
            $("#search-results").append(
              '<li style="padding: 5px 10px; border-bottom: 1px solid #ddd;"><a href="blog-details.php?id=' + eventItem.id_evento + '" style="display:block; text-decoration:none; color:#333;">' + eventItem.nombre + '</a></li>'
            );
          });
          $("#search-results").show();
        } else {
          $("#search-results").hide();
        }
      },
      error: function(){
        $("#search-loader").hide();
        $("#search-results").hide();
      }
    });
  });
});
</script>
