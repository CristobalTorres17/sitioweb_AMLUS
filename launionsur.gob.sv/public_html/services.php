<?php $head_title="Servicios || Alcaldía || Servicios PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "Servicios";
require_once('parts/page-title.php');
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
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Se incluyen jQuery e Isotope para el filtrado y animaciones -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js"></script>

<!--Services Page Start-->
<section class="services-two services-page">
    <div class="services-two__shape-1 img-bounce">
        <img src="assets/images/shapes/services-two-shape-1.png" alt="">
    </div>
    <div class="container">
        <!-- Filtro de Categorías usando estilos existentes -->
        <div class="project-two__filter-box">
            <ul class="project-two__filter style1 post-filter list-unstyled clearfix">
                <li data-filter="*" class="active"><span class="filter-text">Todo</span></li>
                <?php foreach ($tramites as $categoria => $servicios): ?>
                    <?php $catClass = strtolower(str_replace(' ', '', $categoria)); ?>
                    <li data-filter=".<?= $catClass ?>"><span class="filter-text"><?= htmlspecialchars($categoria) ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Lista de Servicios -->
        <div class="row filter-layout">
            <?php foreach ($tramites as $categoria => $servicios): ?>
                <?php $catClass = strtolower(str_replace(' ', '', $categoria)); ?>
                <?php foreach ($servicios as $servicio): ?>
                    <div class="col-xl-4 col-lg-6 filter-item <?= $catClass ?>">
                        <div class="services-two__single wow fadeInUp" data-wow-delay="300ms">
                            <div class="services-two__icon">
                                <span class="<?= htmlspecialchars($servicio['icono']) ?>"></span>
                            </div>
                            <h3 class="services-two__title">
                                <a href="lighting-fixtures.php?nombre=<?= urlencode($servicio['nombre']) ?>">
                                    <?= htmlspecialchars($servicio['nombre']) ?>
                                </a>
                            </h3>
                            <p class="services-two__text">
                                <?= htmlspecialchars($servicio['descripcion']) ?>
                            </p>
                            <a href="lighting-fixtures.php?nombre=<?= urlencode($servicio['nombre']) ?>" class="services-two__learn-more">
                                Más Información <span class="icon-arrow-right"></span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once('parts/home/cta.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"></script>

<script>
$(document).ready(function(){
    var $grid = $('.filter-layout');
    
    // Esperamos a que todas las imágenes se hayan cargado antes de inicializar Isotope
    $grid.imagesLoaded(function(){
        $grid.isotope({
            itemSelector: '.filter-item',
            layoutMode: 'fitRows',
            transitionDuration: '0.6s' // Duración de la animación
        });
    });
    
    // Filtrado al hacer clic en los botones
    $('.project-two__filter li').on('click', function(){
    $('.project-two__filter li').removeClass('active');
    $(this).addClass('active');
    var filterValue = $(this).attr('data-filter');
    $grid.isotope({ filter: filterValue });
    
    // Forzar la re-evaluación de las animaciones WOW
    new WOW().init();
    // O si tienes una instancia global de WOW, por ejemplo:
    // wow.sync();
});

});
</script>


<?php require_once('parts/footer/footer.php'); ?>
<?php require_once('parts/layout/bottom-layout.php'); ?>
