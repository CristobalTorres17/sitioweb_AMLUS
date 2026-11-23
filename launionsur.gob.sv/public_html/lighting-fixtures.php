<?php 
// detalle-servicio.php

// Definir (o incluir) el arreglo completo de servicios
$tramites = [
    'Salud' => [
        [
            'nombre' => 'Atención Médica Primaria',
            'descripcion' => 'Clínicas municipales que ofrecen atención básica y programas de salud preventiva.',
            'icono' => 'fa-solid fa-stethoscope',
            'unidad' => 'Unidad de Salud Comunitaria',
            'detalles' => 'Ofrecemos atención primaria y servicios preventivos para la comunidad.',
            'documentacion' => 'Documento de identidad y registro en el sistema de salud municipal.'
        ],
        [
            'nombre' => 'Control de Plagas',
            'descripcion' => 'Control de plagas en espacios públicos para garantizar la salubridad del municipio.',
            'icono' => 'fa-solid fa-bug',
            'unidad' => 'Unidad de Salud Ambiental',
            'detalles' => 'Jornadas de fumigación en zonas de riesgo para prevenir enfermedades.',
            'documentacion' => 'Solicitud de fumigación y registro de la zona a ser intervenida.'
        ],
        [
            'nombre' => 'Campañas de Vacunación',
            'descripcion' => 'Programas de vacunación para prevenir enfermedades en la comunidad.',
            'icono' => 'fa-solid fa-syringe',
            'unidad' => 'Unidad de Salud Comunitaria',
            'detalles' => 'Campañas de vacunación gratuitas en puntos designados del municipio.',
            'documentacion' => 'Documento de identidad y formulario de consentimiento.'
        ],
        [
            'nombre' => 'Asistencia Veterinaria',
            'descripcion' => 'Servicio de atención y rescate de animales en situación de emergencia.',
            'icono' => 'fa-solid fa-paw',
            'unidad' => 'Unidad de Bienestar Animal',
            'detalles' => 'Asistencia en rescate y atención veterinaria de emergencia para animales en riesgo.',
            'documentacion' => 'Reporte del lugar de la emergencia y contacto del reportante.'
        ],
        [
            'nombre' => 'Servicios de Psicología',
            'descripcion' => 'Atención psicológica gratuita para la comunidad.',
            'icono' => 'fa-solid fa-brain',
            'unidad' => 'Unidad de Bienestar Social',
            'detalles' => 'Apoyo psicológico y terapias para ciudadanos en situación de vulnerabilidad.',
            'documentacion' => 'Documento de identidad y cita previa registrada.'
        ],
        [
            'nombre' => 'Clínicas de Salud Preventiva',
            'descripcion' => 'Servicios de chequeo preventivo en salud.',
            'icono' => 'fa-solid fa-heartbeat',
            'unidad' => 'Unidad de Salud Comunitaria',
            'detalles' => 'Chequeos regulares para la prevención de enfermedades crónicas.',
            'documentacion' => 'Documento de identidad y ficha médica actualizada.'
        ]
    ],
    'Infraestructura' => [
        [
            'nombre' => 'Alumbrado Público',
            'descripcion' => 'Instalación y mantenimiento de alumbrado en calles del municipio.',
            'icono' => 'fa-solid fa-lightbulb',
            'unidad' => 'Unidad de Obras Públicas',
            'detalles' => 'Iluminación de espacios públicos para mejorar la seguridad.',
            'documentacion' => 'Solicitud de alumbrado y ubicación de la zona a intervenir.'
        ],
        [
            'nombre' => 'Mantenimiento de Parques y Jardines',
            'descripcion' => 'Cuidado de parques y áreas verdes municipales.',
            'icono' => 'fa-solid fa-tree',
            'unidad' => 'Unidad de Medio Ambiente',
            'detalles' => 'Mantenimiento de infraestructura recreativa y limpieza.',
            'documentacion' => 'Solicitud de mantenimiento y ubicación del parque.'
        ],
        [
            'nombre' => 'Reparación de Calles',
            'descripcion' => 'Mantenimiento y reparación de calles municipales.',
            'icono' => 'fa-solid fa-road',
            'unidad' => 'Unidad de Infraestructura Vial',
            'detalles' => 'Reparación de vías públicas para mejorar el tránsito.',
            'documentacion' => 'Reporte de daños en la vía y autorización de la unidad responsable.'
        ],
        [
            'nombre' => 'Recolección de Basura',
            'descripcion' => 'Servicio de recolección de residuos en zonas urbanas.',
            'icono' => 'fa-solid fa-trash-alt',
            'unidad' => 'Unidad de Servicios Municipales',
            'detalles' => 'Recogida semanal de residuos sólidos.',
            'documentacion' => 'Registro de ubicación y horario de recolección asignado.'
        ],
        [
            'nombre' => 'Construcción de Infraestructura Pública',
            'descripcion' => 'Construcción de nuevas infraestructuras para la comunidad.',
            'icono' => 'fa-solid fa-building',
            'unidad' => 'Unidad de Obras Públicas',
            'detalles' => 'Construcción de espacios recreativos y educativos.',
            'documentacion' => 'Plan de construcción y permisos aprobados por la alcaldía.'
        ],
        [
            'nombre' => 'Limpieza de Alcantarillado',
            'descripcion' => 'Limpieza de sistemas de drenaje y alcantarillado público.',
            'icono' => 'fa-solid fa-water',
            'unidad' => 'Unidad de Infraestructura Vial',
            'detalles' => 'Mantenimiento preventivo de sistemas de alcantarillado.',
            'documentacion' => 'Solicitud de limpieza y ubicación específica del drenaje.'
        ]
    ],
    'Asistencia Social' => [
        [
            'nombre' => 'Asistencia a Familias Vulnerables',
            'descripcion' => 'Apoyo a familias en condiciones de pobreza extrema.',
            'icono' => 'fa-solid fa-hands-helping',
            'unidad' => 'Unidad de Bienestar Social',
            'detalles' => 'Entrega de alimentos y apoyo económico.',
            'documentacion' => 'Estudio socioeconómico y documento de identidad de los beneficiarios.'
        ],
        [
            'nombre' => 'Ayuda para Personas con Discapacidad',
            'descripcion' => 'Programa de apoyo para personas con discapacidad.',
            'icono' => 'fa-solid fa-wheelchair',
            'unidad' => 'Unidad de Bienestar Social',
            'detalles' => 'Asistencia médica y económica para personas con discapacidades.',
            'documentacion' => 'Certificado de discapacidad y documento de identidad.'
        ],
        [
            'nombre' => 'Programas de Vivienda',
            'descripcion' => 'Apoyo en construcción y mejora de viviendas.',
            'icono' => 'fa-solid fa-home',
            'unidad' => 'Unidad de Vivienda Social',
            'detalles' => 'Asesoría y financiamiento en mejoras habitacionales.',
            'documentacion' => 'Documento de identidad y solicitud de ayuda habitacional.'
        ],
        [
            'nombre' => 'Distribución de Ropa y Calzado',
            'descripcion' => 'Entrega de ropa y calzado a familias necesitadas.',
            'icono' => 'fa-solid fa-tshirt',
            'unidad' => 'Unidad de Bienestar Social',
            'detalles' => 'Distribución de donaciones de ropa y calzado.',
            'documentacion' => 'Registro de beneficiarios y documento de identidad.'
        ],
        [
            'nombre' => 'Atención a Adultos Mayores',
            'descripcion' => 'Apoyo integral a la tercera edad.',
            'icono' => 'fa-solid fa-user',
            'unidad' => 'Unidad de Bienestar Social',
            'detalles' => 'Programas de atención y cuidados para adultos mayores.',
            'documentacion' => 'Documento de identidad y registro en programas de apoyo.'
        ],
        [
            'nombre' => 'Servicios de Comedores Comunitarios',
            'descripcion' => 'Acceso a comedores comunitarios gratuitos.',
            'icono' => 'fa-solid fa-utensils',
            'unidad' => 'Unidad de Asistencia Social',
            'detalles' => 'Comidas diarias para personas en situación de vulnerabilidad.',
            'documentacion' => 'Registro en el comedor comunitario y documento de identidad.'
        ]
    ]
];

// Recuperar el nombre del servicio desde el parámetro GET
$nombreServicio = $_GET['nombre'] ?? '';
$servicioSeleccionado = null;
$categoriaSeleccionada = '';

// Buscar el servicio en el arreglo
foreach ($tramites as $categoria => $servicios) {
    foreach ($servicios as $servicio) {
        if ($servicio['nombre'] === $nombreServicio) {
            $servicioSeleccionado = $servicio;
            $categoriaSeleccionada = $categoria;
            break 2;
        }
    }
}

// Si no se encuentra el servicio, se muestra un mensaje de error
if (!$servicioSeleccionado) {
    echo "Servicio no encontrado.";
    exit;
}

// Definir el título de la página según el servicio seleccionado
$head_title = $servicioSeleccionado['nombre'] . " || erepair || erepair PHP Template";

require_once('parts/layout/top-layout.php'); 
require_once('parts/header/header2.php'); 
$page_title = $servicioSeleccionado['nombre'];
require_once('parts/page-title.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<style>
.service-details__img i {
    font-size: 80px;  /* Ajusta el tamaño según lo necesites */
    color: #333;      /* Define el color deseado */
    display: block;   /* Si es necesario, para que ocupe el espacio completo */
}
</style>
<!--Service Details Start-->
<section class="service-details">
  <div class="container">
    <div class="row">
      <!-- Contenido principal: detalles completos del servicio -->
      <div class="col-xl-8 col-lg-7">
        <div class="service-details__left">
          <div class="service-details__img">
            <!-- Se muestra el icono en lugar de una imagen -->
            <i class="<?= htmlspecialchars($servicioSeleccionado['icono']) ?>"></i>
          </div>
          <h3 class="service-details__title-1"><?= htmlspecialchars($servicioSeleccionado['nombre']) ?></h3>
          <p><strong>Descripción:</strong> <?= htmlspecialchars($servicioSeleccionado['descripcion']) ?></p>
          <p><strong>Unidad Responsable:</strong> <?= htmlspecialchars($servicioSeleccionado['unidad']) ?></p>
          <p><strong>Detalles:</strong> <?= htmlspecialchars($servicioSeleccionado['detalles']) ?></p>
          <p><strong>Documentación Requerida:</strong> <?= htmlspecialchars($servicioSeleccionado['documentacion']) ?></p>
        </div>
      </div>
      <!-- Sidebar: mostrar solo los servicios de la misma categoría para reducir saturación -->
      <div class="col-xl-4 col-lg-5">
        <div class="service-details__sidebar">
          <div class="service-details__services-box">
            <h3 class="service-details__services-title">Servicios en <?= htmlspecialchars($categoriaSeleccionada) ?></h3>
            <ul class="service-details__services-list list-unstyled">
              <?php foreach ($tramites[$categoriaSeleccionada] as $servicio): ?>
                <li class="<?= ($servicio['nombre'] === $servicioSeleccionado['nombre']) ? 'active' : '' ?>">
                  <a href="lighting-fixtures.php?nombre=<?= urlencode($servicio['nombre']) ?>">
                    <?= htmlspecialchars($servicio['nombre']) ?>
                    <span class="icon-arrow-right"></span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <!-- Aquí podrías agregar contenido adicional al sidebar si lo requieres -->
        </div>
      </div>
    </div>
  </div>
</section>
<!--Service Details End-->

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
