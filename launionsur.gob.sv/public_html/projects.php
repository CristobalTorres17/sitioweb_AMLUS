<?php 
$head_title="Distritos || La Unión Sur || Alcaldias PHP Template";
require_once('parts/layout/top-layout.php'); 
?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php 
$page_title = "Distritos";
require_once('parts/page-title.php');
?>

<div class="container">
  <!-- Encabezado: Texto sobre el mapa -->
  <div class="map-header">
    <div class="project-one__top">
      <div class="section-title text-left">
        <div class="section-title__tagline-box">
          <span class="section-title__tagline">Galeria</span>
        </div>
        <div class="section-title__title-box sec-title-animation animation-style2">
          <h2 class="section-title__title title-animation">
            La Unión Sur <br>Nuestros Ocho Distritos
          </h2>
        </div>
      </div>
      <div class="project-one__btn-box">
        <a href="#" class="project-one__btn thm-btn">Galeria</a>
      </div>
    </div>
  </div>

  <!-- Contenedor para el mapa y la historia -->
  <div class="map-history-container">
    <!-- Mapa -->
    <div id="map-container" class="map-container"></div>
    <!-- Tooltip -->
    <div id="tooltip" class="tooltip"></div>
    <!-- Bloque de texto para la Historia de La Unión -->
    <div class="history-block">
      <h2>Historia de La Unión</h2>
      <p>
        La ciudad de La Unión, fundada en el siglo XVIII, es conocida por su belleza natural
        y sus raíces históricas. A lo largo de los años, este lugar ha crecido hasta 
        convertirse en un punto clave de la región oriental de El Salvador, con un puerto 
        que representa un importante motor económico y cultural. Su gente y tradiciones,
        llenas de calidez y riqueza patrimonial, conforman la esencia de La Unión, que hoy
        se proyecta como un territorio de grandes oportunidades turísticas y de desarrollo
        para toda la población salvadoreña.
      </p>
    </div>
  </div>
</div>

<!-- Estilos para el encabezado, mapa, tooltip y bloque de historia -->
<style>
  /* Encabezado sobre el mapa */
  .map-header {
    margin-bottom: 30px;
  }
  
  /* Contenedor que agrupa el mapa y la historia */
  .map-history-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: row;
    min-height: 100vh;
    gap: 30px;
    padding: 20px;
  }
  
  /* Contenedor del mapa */
  .map-container {
    position: relative;
    width: 747px; /* Ajusta esto al ancho de tu SVG */
  }
  
  /* Tooltip para mostrar el nombre del municipio e imagen */
  .tooltip {
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 9999;
    background-color: rgba(0, 0, 0, 0.7);
    padding: 10px;
    border-radius: 8px;
    color: #fff;
    font-size: 0.9rem;
    position: absolute;
  }
  
  /* Estilos para la imagen dentro del tooltip */
  .tooltip img {
    display: block;
    margin-top: 5px;
    max-width: 200px;
    border: 2px solid #fff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.4);
  }
  
  /* Clase general para los municipios */
  .municipio {
    transition: transform 0.3s ease-out, fill 0.3s ease-out;
    transform-origin: center center;
  }
  
  /* Clase para el distrito activo (hover) */
  .active {
    fill: #ffcc00 !important;
    transform: scale(1.05) !important;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
  }
  
  /* Bloque de texto para la Historia */
  .history-block {
    max-width: 350px;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-family: 'Georgia', serif;
  }
  
  .history-block h2 {
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
  }
  
  .history-block p {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
  }
  
  /* Responsividad: en pantallas pequeñas se apilan los elementos */
  @media (max-width: 992px) {
    .map-history-container {
      flex-direction: column;
      align-items: stretch;
    }
    
    .map-container, .history-block {
      width: 100%;
      max-width: 100%;
    }
    
    .history-block {
      margin-top: 20px;
    }
  }
</style>

<section class="project-one">
  <div class="container">
    <div class="project-one__top">
      <div class="section-title text-left">
        <div class="section-title__tagline-box">
          <span class="section-title__tagline">Galeria</span>
        </div>
        <div class="section-title__title-box sec-title-animation animation-style2">
          <h2 class="section-title__title title-animation">
            La Unión Sur <br>Nuestros Ocho Distritos
          </h2>
        </div>
      </div>
 
    </div>
    <div class="row">
      <!--Project One Single Start-->
      <div class="col-xl-6 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="100ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-1.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-1.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Juramentación de ADESCO</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Cantón Las Pozas, Conchagua.</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->
      
      <!--Project One Single Start-->
      <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-2.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-2.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Juramentación de ADESCO</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Cantón Las Pozas, Conchagua.</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->

      <!--Project One Single Start-->
      <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="600ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-3.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-3.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Celebración de la niñez</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Cantón Coyolito</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->

      <!--Project One Single Start-->
      <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" data-wow-delay="900ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-4.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-4.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Proyectos</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Distrito de San Alejo</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->

      <!--Project One Single Start-->
      <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="1000ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-5.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-5.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Celebración de la Niñez</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Cantón Coyolito</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->

      <!--Project One Single Start-->
      <div class="col-xl-6 col-lg-6 col-md-6 wow fadeInRight" data-wow-delay="1100ms">
        <div class="project-one__single">
          <div class="project-one__img-box">
            <div class="project-one__img">
              <img src="assets/images/project/project-1-6.jpg" alt="">
              <div class="project-one__arrow">
                <a href="assets/images/project/project-1-6.jpg" class="img-popup">
                  <span class="icon-arrow-right"></span>
                </a>
              </div>
            </div>
            <div class="project-one__content">
              <p class="project-one__sub-title">Inicio de las Fiestas</p>
              <h3 class="project-one__title">
                <a href="project-details.php">Distrito de Yayantique</a>
              </h3>
            </div>
          </div>
        </div>
      </div>
      <!--Project One Single End-->
    </div>
  </div>
</section>

<!-- Script para cargar el SVG y gestionar el tooltip -->
<script>
  // Objeto que mapea el id de cada distrito a su imagen correspondiente
  const districtImages = {
    'path22': 'San alejo.jpg',
    'path23': 'La union.jpg',
    'path24': 'intipuca.jpg',
    'path25': 'Conchagua.jpg',
    'path26': 'La union.jpg',
    'path27': 'meanguera.jpg',
    'path28': 'imagenes/distrito7.png',
    'path29': 'imagenes/distrito8.png',
    'path21': 'El carmen.jpg',
    'path20': 'Yayantique.jpg',
    'path19': 'Yucuaiquín.jpg'
    // Agrega aquí más mapeos según tus necesidades
  };

  // Cargar el archivo SVG de forma asíncrona e inyectarlo en el DOM
  fetch('mapa.svg')
    .then(response => response.text())
    .then(svgText => {
      document.getElementById('map-container').innerHTML = svgText;
      const svgDoc = document.querySelector('#map-container svg');
      const tooltip = document.getElementById('tooltip');

      // Selecciona todos los paths que tengan el atributo inkscape:label
      const municipios = svgDoc.querySelectorAll('path[inkscape\\:label]');
      console.log("Municipios encontrados:", municipios);

      municipios.forEach(muni => {
        muni.classList.add('municipio');

        muni.addEventListener('mouseenter', function() {
          let nombre = muni.getAttribute('inkscape:label') || muni.id || "Sin nombre";

          // Ejemplo: si el id es 'La union2', se sobrescribe el nombre a "San Alejo"
          if (muni.id === 'La union2') {
            nombre = "San Alejo";
          }

          // Obtiene la imagen asociada al distrito o usa una imagen por defecto
          const imageSrc = districtImages[muni.id] || 'imagenes/distrito.jpg';
          tooltip.innerHTML = `<strong>${nombre}</strong><br>
                               <img src="${imageSrc}" alt="Imagen de ${nombre}">`;
          muni.classList.add('active');
        });

        muni.addEventListener('mousemove', function(e) {
          tooltip.style.left = (e.pageX + 10) + 'px';
          tooltip.style.top = (e.pageY + 10) + 'px';
          tooltip.style.opacity = 1;
        });

        muni.addEventListener('mouseleave', function() {
          tooltip.style.opacity = 0;
          muni.classList.remove('active');
        });
      });
    })
    .catch(error => console.error("Error cargando el SVG:", error));
</script>

<?php 
require_once('parts/home/cta.php'); 
require_once('parts/footer/footer.php'); 
require_once('parts/layout/bottom-layout.php'); 
?>
