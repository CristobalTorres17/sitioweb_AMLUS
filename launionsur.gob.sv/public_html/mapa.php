<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mapa Interactivo - La Unión Sur</title>
  <!-- CDN de Bootstrap 5 (ajusta la versión si lo requieres) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Contenedor para el mapa */
    #map-container {
      position: relative;
      width: 747px; /* Ajusta esto al ancho de tu SVG */
      margin: 0 auto;
    }

    /* Tooltip para mostrar el nombre del municipio e imagen */
    #tooltip {
      pointer-events: none; /* Para que no interfiera con el cursor */
      opacity: 0;           /* Oculto inicialmente */
      transition: opacity 0.2s;
      z-index: 9999;        /* Aseguramos que quede por encima */
      background-color: rgba(0, 0, 0, 0.7);
      padding: 10px;
      border-radius: 8px;
      color: #fff;
      font-size: 0.9rem;
    }

    /* Estilos para la imagen dentro del tooltip */
    #tooltip img {
      display: block;
      margin-top: 5px;
      max-width: 200px; /* Ajusta según tu preferencia */
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
  </style>
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div id="map-container"></div>
  <div id="tooltip" class="position-absolute"></div>
</div>

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
    'path19': 'Yucuaiquín.jpg',
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

          // Ejemplo: si el id es 'path22', se sobrescribe el nombre a "San Alejo"
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

</body>
</html>
