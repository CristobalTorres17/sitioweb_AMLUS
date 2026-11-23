<?php $head_title="Trámites || Alcaldia || Trámites PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>

<?php $head_title="Short Circuit Repair || erepair || erepair PHP Template"?>

<?php require_once('parts/layout/top-layout.php'); ?>

<!-- header -->
<?php require_once('parts/header/header2.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
      body {
        
    background-color: rgb(248, 248, 250);
    color: white;
}

.nav-tabs .nav-link {
    color: rgb(3, 3, 3);
}

.nav-tabs .nav-link.active {
    background-color:rgb(35, 52, 68);
    color: white;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 300; 
    color:rgb(33, 35, 63); 
}

.card {
    background-color: rgba(253, 254, 255, 0.8); 
    color:rgb(11, 12, 14); 
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
    height: 250px; 
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border: none;
    border-radius: 10px; 
}

.card:hover {
    transform: scale(1.05);
}


.card i {
    color:rgb(3, 112, 236); 
}

.modal-xl {
    max-width: 90%;
}



.services-two__text {
    font-size: 1.0rem; 
    font-weight: 500; 
    color: rgb(50, 50, 50); 
    margin-top: 10px; 
    line-height: 1.5; 
}

.services-two__learn-more {
    color: rgb(11, 12, 14); 
    font-weight: 600; 
    text-decoration: none;
    margin-top: 10px;
}

.services-two__learn-more:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

<div class="container mt-0">
    

  
    <?php  $page_title = "Trámites";
require_once('parts/page-title.php');
        $tramites = [
                 "Trámites Legales" => [
                    [
                        "nombre" => "Legalización de Documentos",
                        "descripcion" => "Proceso de validación legal de documentos municipales.",
                        "costo" => "$20",
                        "tiempo" => "2 días hábiles",
                        "requisitos" => "Documento original, identificación.",
                        "documentacion" => "Documento a legalizar, copia de identificación.",
                        "pasos" => "1. Acudir a la alcaldía. 2. Presentar documento. 3. Pagar tasa.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-signature"
                    ]
                ],
                "Constancias" => [
                    [
                        "nombre" => "Solicitud para Acta Catastral de ANDA",
                        "descripcion" => "Solicitud para obtener el acta catastral de ANDA.",
                        "costo" => "$10.50",
                        "tiempo" => "2 días hábiles",
                        "requisitos" => "Estar solvente con el pago de tasas por servicios del inmueble. El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.",
                        "documentacion" => "1. Copia de escritura del inmueble. 2. Copia de DUI del propietario. 3. Solvencia del inmueble.",
                        "pasos" => "1. Presentar toda la documentación al departamento de registro tributario de esta municipalidad. 2. Esperar la notificación a través de llamada para la respectiva cancelación de la constancia. 3. Luego de la cancelación, pasar por el retiro de la constancia en el departamento de registro tributario de esta municipalidad.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-map"
                    ],
                    [
                        "nombre" => "Solicitud para Acta Catastral EEO",
                        "descripcion" => "Solicitud para obtener el acta catastral EEO.",
                        "costo" => "$10.50",
                        "tiempo" => "2 días hábiles",
                        "requisitos" => "Estar solvente con el pago de tasas por servicios del inmueble. El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.",
                        "documentacion" => "1. Copia de escritura del inmueble. 2. Copia de DUI del propietario. 3. Solvencia del inmueble.",
                        "pasos" => "1. Presentar toda la documentación al departamento de registro tributario de esta municipalidad. 2. Esperar la notificación a través de llamada para la respectiva cancelación de la constancia. 3. Luego de la cancelación, pasar por el retiro de la constancia en el departamento de registro tributario de esta municipalidad.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-map"
                    ]
                    ],
                    "Trámites Empresariales" => [
                    [
                        "nombre" => "Vialidad Municipal",
                        "descripcion" => "Vialidad y Solvencia Municipal Inscrito.",
                        "costo" => "$6.06",
                        "tiempo" => "1 día hábil",
                        "requisitos" => "Estar inscrito en el sistema de Registro Tributario Municipal y estar solvente de todas las propiedades.",
                        "documentacion" => "Copia de DUI y escritura de la propiedad.",
                        "pasos" => "1. Solicitar solvencia. 2. Validar pagos. 3. Emitir vialidad o solvencia.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-id-card"
                    ],
                    [
                        "nombre" => "Vialidad No Rural",
                        "descripcion" => "Vialidad y Solvencia Municipal No Inscrito Rural.",
                        "costo" => "$21.81",
                        "tiempo" => "1 día hábil",
                        "requisitos" => "1. Dependen de la inspección que se le realice al usuario, en la unidad de Fiscalización, en tal caso este recibiendo servicios la propiedad, se le prescribirá en el plazo de tres años, con facultad en el Art. 62 ley general tributaria municipal. 2. Estar solvente con todas las propiedades.",
                        "documentacion" => "Copia de Escritura, DUI.",
                        "pasos" => "1. Presentar copia de DUI, escritura en atención al usuario. 2. Esperar indicaciones.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-city"
                    ],
                    [
                        "nombre" => "Vialidad No Urbano",
                        "descripcion" => "Vialidad y Solvencia Municipal No Inscrito Urbano.",
                        "costo" => "$16.53",
                        "tiempo" => "1 día hábil",
                        "requisitos" => "1. Dependen de la inspección que se le realice al usuario, en la unidad de Fiscalización, en tal caso este recibiendo servicios la propiedad, se le prescribirá en el plazo de tres años, con facultad en el Art. 62 ley general tributaria municipal. 2. Estar solvente con todas las propiedades.",
                        "documentacion" => "Copia de Escritura, DUI.",
                        "pasos" => "1. Presentar copia de DUI, escritura en atención al usuario. 2. Esperar indicaciones.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-mountain"
                    ],
                    [
                        "nombre" => "Estado de Cuenta Inmueble",
                        "descripcion" => "Emisión de Estado de Cuenta Inmueble.",
                        "costo" => "Gratis",
                        "tiempo" => "15 días hábiles",
                        "requisitos" => "Registro de empresa, identificación.",
                        "documentacion" => "Certificado de registro, documento de identidad.",
                        "pasos" => "1. Presentar solicitud. 2. Validación de empresa. 3. Emitir licencia.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-building"
                    ],
                    [
                        "nombre" => "Estado de Cuenta Comercio",
                        "descripcion" => "Emisión de Estado de Cuenta Comercio.",
                        "costo" => "Gratis",
                        "tiempo" => "5 días hábiles",
                        "requisitos" => "Identificación, documento de la empresa.",
                        "documentacion" => "Identificación, documento vigente.",
                        "pasos" => "1. Solicitar renovación. 2. Validar información. 3. Emitir permiso.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-invoice-dollar"
                    ],
                    [
                        "nombre" => "Convenio de Inmueble",
                        "descripcion" => "Convenio de Pago Inmueble.",
                        "costo" => "Gratis",
                        "tiempo" => "10 días hábiles",
                        "requisitos" => "Documentación oficial, registro de empresa.",
                        "documentacion" => "Registro comercial, identificación.",
                        "pasos" => "1. Solicitar licencia. 2. Validar datos. 3. Emitir licencia.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-invoice"
                    ],
                    [
                        "nombre" => "Convenio de Comercio",
                        "descripcion" => "Convenio de Pago Comercio.",
                        "costo" => "Gratis",
                        "tiempo" => "2 días hábiles",
                        "requisitos" => "Número de cuenta, identificación.",
                        "documentacion" => "Número de cuenta, identificación oficial.",
                        "pasos" => "1. Solicitar estado. 2. Validar información. 3. Emitir estado.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-invoice"
                    ],
                    [
                        "nombre" => "Pago de Impuestos",
                        "descripcion" => "Pago de Impuestos y Tasas Municipales.",
                        "costo" => "Gratis",
                        "tiempo" => "3 días hábiles",
                        "requisitos" => "Identificación, monto adeudado.",
                        "documentacion" => "Copia de deuda, identificación.",
                        "pasos" => "1. Solicitar plan. 2. Evaluar deuda. 3. Emitir plan.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-credit-card"
                    ]
                    ],
                    "Registro Familiar" => [
                        
                            [
                                "nombre" => "Asentamiento de Nacimiento",
                                "descripcion" => "Inscripción de Nacimiento.",
                                "costo" => "Gratis",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "Se debe de informar en un plazo máximo de 45 días en base al art 51, Art 48 LREF.",
                                "documentacion" => "1. Ficha médica del nacimiento emitida por Sistema Nacional Integrado de Salud (PREREGISTRO NUI). 2. Copia de DUI de ambos padres si aplicare.",
                                "pasos" => "1. La persona informante deberá, demostrar la ocurrencia del nacimiento al registrador del estado familiar, mediante la declaración de un testigo, mayor de dieciocho años de edad, que pueda dar fé conforme a lo dispuesto en esta ley. 2. No existen limitaciones en cuanto a que el testigo sea familiar de la madre o padre del recién nacido.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-baby"
                            ],
                            [
                                "nombre" => "Emisión de Auténtica de Partidas",
                                "descripcion" => "Autenticación oficial de las partidas.",
                                "costo" => "$9.98",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "Se deberá presentar prueba documental si se solicita la modificación del nombre o apellidos de uno de los padres del interesado.",
                                "documentacion" => "Partida original, identificación.",
                                "pasos" => "1. Indicar el tipo de firma requerida. 2. Pagar el costo del trámite. 3. Emitir auténtica.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-id-card"
                            ],
                            [
                                "nombre" => "Carnét de Identificación Personal",
                                "descripcion" => "Emisión de carnét de menoridad.",
                                "costo" => "$5.00",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "Debe estar acompañado por al menos uno de sus padres o representante legal, el menor debe tener entre 7 y 17 años.",
                                "documentacion" => "Certificación de partida de nacimiento reciente, debe de residir dentro del municipio y acompañarse con uno de sus padres o responsable.",
                                "pasos" => "1. Acudir a la Alcaldía con el menor y la documentación requerida. 2. Presentar la partida de nacimiento y la copia del DUI del acompañante. 3. Pagar el trámite. 4. Esperar la emisión del carné.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-user"
                            ],
                            [
                                "nombre" => "Constancias",
                                "descripcion" => "Constancia de Inexistencia (CI), Constancia de Solteria (CS)",
                                "costo" => "$3.68",
                                "tiempo" => "5 día hábil",
                                "requisitos" => "Identificación, solicitud.",
                                "documentacion" => "Presentar solicitud con datos personales como el nombre, lugar de nacimiento, fecha de nacimiento y nombre completo de los padres (CI),   2. Certificación de partida de nacimiento reciente, copia de DUI (CS).",
                                "pasos" => "1. Presentar la solicitud con los datos personales. 2. Realizar el pago del trámite. 3. Recibir la constancia.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-id-badge"
                            ],
                            [
                                "nombre" => "Marginaciones y/o Reposiciones",
                                "descripcion" => "Marginaciones (MG), Reposiciones (RP).",
                                "costo" => "$3.68",
                                "tiempo" => "1 día hábil",
                                "requisitos" => "Solicitud escrita dirigida al Registro del Estado Familiar, especificando el tipo de marginación solicitada (MG). Documentos probatorios (MG), (RP).",
                                "documentacion" => "1. Documentos probatorios que respalden la modificación (MG) 2. Copia del DUI del solicitante (MG),(RP). 3. Se realiza a solicitud de la parte interesada. 4. Llenado de formulario y solicitud (RP). 5. Partida certificada por Notario o Microfilm emitidopor RNPN (RP)",
                                "pasos" => "1. Presentarse a la unidad de Registro Familiar. 2. Pagar aranceles.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-receipt"
                            ],
                                [
                                    "nombre" => "Certificaciones",
                                    "descripcion" => "Certificación de Partidas de Nacimiento (CPN), Matrimonio (CM), Partida de Defunción (CPD), Unión No Matrimonial (CUNM), Divorcios Históricos (CDH), Cedula de Identidad (CCI)..",
                                    "costo" => "$3.68",
                                    "tiempo" => "5 días hábiles",
                                    "requisitos" => "a. Presentar solicitud con datos personales (nombre, lugar y fecha de nacimiento)(CPN) b. Presentar solicitud con nombres completos de los contrayentes fecha y lugar (CM). c. Presentar solicitud con el nombre del fallecido, fecha y lugar del fallecimiento, nombre de los padres o cónyuge (si aplica) (CPD).  d. Presentar solicitud con los nombres de los convivientes, fecha y lugar de la unión (CUNM). e. Presentar solicitud con nombres completos de las partes involucradas, fecha y lugar del divorcio (CDH). f. Presentar solicitud con fecha exacta de emisión de cédula a solicitar, y nombre completo de los padres(CCI).",
                                    "documentacion" => "Copia del DUI del solicitante (CNP),(CM),(CPD),(CDH), Copia del DUI de ambos convivientes (CUNM).",
                                    "pasos" => "1. Presentarse a la Alcaldía con la documentación, en unidad de registro familiar (REF). 2. Llenar la solicitud con los datos para cualquiera de su certificación. 3. Pagar el arancel establecido, o costo del tramite 4. Emitir certificación.",
                                    "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                    "icono" => "fa-solid fa-certificate"
                                ],
                            
                            
                            [
                                "nombre" => "Asentamiento de Defunción",
                                "descripcion" => "Registro de defunción.",
                                "costo" => "$3.68",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "Cualquier pariente por consanguinidad o afinidad de un fallecido debe informarlo al registrador del estado familiar.",
                                "documentacion" => "Esquela del Instituto de Medicina Legal, constancia médica o certificado firmado por el médico que atendió el cadáver.",
                                "pasos" => "1. Si no es posible obtener constancia médica, el deceso debe probarse con autopsia o testigo.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-cross"
                            ],
                            [
                                "nombre" => "Inscripciones Uniones No Matrimoniales",
                                "descripcion" => "Registro de unión no matrimonial.",
                                "costo" => "$3.68",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "Certificación de la sentencia que declara la existencia de la unión.",
                                "documentacion" => "Certificación de Sentencia ejecutoriada por medio de oficio por el Juez competente.",
                                "pasos" => "1. Presentarse a la Alcaldía con la documentación. 2. Llenar la solicitud. 3. Pagar el arancel establecido.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-handshake"
                            ],
                            [
                                "nombre" => "Inscripciones de Matrimonios",
                                "descripcion" => "Registro de matrimonio.",
                                "costo" => "$3.68",
                                "tiempo" => "5 días hábiles",
                                "requisitos" => "El funcionario autorizado deberá remitir al registrador del Estado Familiar la certificación del acta de celebración del matrimonio.",
                                "documentacion" => "Testimonio de escritura pública de la celebración del matrimonio.",
                                "pasos" => "1. Presentarse a la Alcaldía con la documentación. 2. Llenar la solicitud. 3. Pagar el arancel establecido.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-ring"
                            ]
                            ],
                   "Inmuebles" => [
                    [
                        "nombre" => "Inscripción de Inmueble",
                        "descripcion" => "Inscripción de un inmueble en el registro.",
                        "costo" => "$ -",
                        "tiempo" => "3 días hábiles",
                        "requisitos" => "El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.",
                        "documentacion" => "1. Copia de escritura del inmueble. 2. Copia de DUI del propietario del inmueble.",
                        "pasos" => "1. Presentar toda la documentación al departamento de registro tributario de esta municipalidad. 2. Esperar la notificación de la inspección, para verificación de servicios recibidos en el inmueble, para la respectiva calificación. 3. Notificación del pago mensual de tasas por servicios municipales.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-home"
                    ],
                    [
                        "nombre" => "Solicitud para Inscripción de Empresas o Negocios",
                        "descripcion" => "Solicitud para inscribir empresas o negocios.",
                        "costo" => "$ -",
                        "tiempo" => "3 días hábiles",
                        "requisitos" => "El trámite debe ser personal. En caso contrario, el representante debe presentar un poder especial.",
                        "documentacion" => "1. Copia de escritura del inmueble. 2. Copia de DUI del propietario del inmueble. 3. Copia de escritura de constitución (si es asociación). 4. Balance inicial. 5. Declaración jurada. 6. Solvencia municipal. 7. Vialidad.",
                        "pasos" => "1. Presentar toda la documentación al departamento de registro tributario de esta municipalidad. 2. Esperar la notificación a través de llamada o de forma presencial de la determinación de pago de impuestos por mes de la empresa o negocio y firmar dicho documento. 3. Luego de la cancelación, pasar por el retiro de la constancia en el departamento de registro tributario de esta municipalidad.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-briefcase"
                    ]
                    ],
               
                    "Trámites Cementerio" => [
                    
                        [
                            "nombre" => "Enterramiento", 
                            "descripcion" => "Enterramiento en fábrica ínfima y prórroga de un año para conservar cadáver en la misma sepultura.",
                            "costo" => "$8.46",
                            "tiempo" => "4 días hábiles",
                            "requisitos" => "Fotocopia del DUI del fallecido.",
                            "documentacion" => "Fotocopia del DUI del tramitante.",
                            "pasos" => "1. Presentar ambas fotocopias de DUI en la unidad de cementerio. 2. Validación de datos. 3. Emitir certificación.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-skull"
                          ],
                          [
                            "nombre" => "Remodelación de Bóveda", 
                            "descripcion" => "Proceso de renovación o reparación de una bóveda existente.",
                            "costo" => "Según presupuesto de remodelación",
                            "tiempo" => "2 días hábiles",
                            "requisitos" => "Título de perpetuidad.",
                            "documentacion" => "Fotocopia del DUI.",
                            "pasos" => "1. Presentar solicitud. 2. Entregar fotocopia del DUI y número del título de perpetuidad en la unidad de cementerio. 3. Aprobación del permiso. 4. Inicio de remodelación.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-tools"
                          ],
                          [
                            "nombre" => "Construcción de Nichos Tamaño estándar", 
                            "descripcion" => "Solicitud para construir dos nichos en el cementerio.",
                            "costo" => "$74.19",
                            "tiempo" => "2 días hábiles",
                            "requisitos" => "Título de perpetuidad.",
                            "documentacion" => "Entregar fotocopia del DUI en la unidad de cementerio.",
                            "pasos" => "1. Presentar identificación. 2. Evaluación técnica. 3. Aprobación de construcción.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-hard-hat"
                          ],
                          [
                            "nombre" => "Derecho a Perpetuidad de Puesto", 
                            "descripcion" => "Adquisición del derecho a perpetuidad por un puesto que mide 2.50x1.20 mts2.",
                            "costo" => "$47.94",
                            "tiempo" => "1 día hábil",
                            "requisitos" => "Título de perpetuidad.",
                            "documentacion" => "Copia de identificación DUI.",
                            "pasos" => "1. Presentar identificación. 2. Validación del espacio disponible. 3. Firma del contrato.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-scroll"
                          ],
                          [
                            "nombre" => "Prórrogas", 
                            "descripcion" => "Prórroga de 7 años para conservar cadáver en misma sepultura.",
                            "costo" => "$15.75",
                            "tiempo" => "2 días hábiles",
                            "requisitos" => "Título de perpetuidad, autorización de la familia.",
                            "documentacion" => "Número del título de perpetuidad y último recibo de pago.",
                            "pasos" => "1. Presentar solicitud. 2. Evaluación del espacio. 3. Firma de prórroga.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-clock"
                          ],
                          [
                            "nombre" => "Abrir y cerrar nicho", 
                            "descripcion" => "Servicio de apertura y cierre de nicho en caso de exhumación o entierro.",
                            "costo" => "$21.06",
                            "tiempo" => "1 día hábil",
                            "requisitos" => "Autorización familiar, permiso del cementerio, título de perpetuidad.",
                            "documentacion" => "Fotocopia del DUI.",
                            "pasos" => "1. Presentar solicitud. 2. Entregar fotocopia del DUI y número del título de perpetuidad en la unidad de cementerio. 3. Programar apertura/cierre.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-door-open"
                          ],
                          [
                            "nombre" => "Extracción de osamenta para traslado", 
                            "descripcion" => "Procedimiento para retirar restos óseos y trasladarlos a otro lugar.",
                            "costo" => "$26.82",
                            "tiempo" => "2 días hábiles",
                            "requisitos" => "Título de perpetuidad, partida de defunción, autorización del ministerio de salud, título de perpetuidad de destino de la osamenta.",
                            "documentacion" => "Fotocopias de DUI del tramitante y del fallecido.",
                            "pasos" => "1. Presentar solicitud. 2. Entregar toda la documentación en la unidad de cementerio. 3. Programar extracción y traslado.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-bone"
                          ],
                          [
                            "nombre" => "Carátula del título de perpetuidad", 
                            "descripcion" => "Reemplazo o actualización de la carátula del título de perpetuidad.",
                            "costo" => "$2.10",
                            "tiempo" => "3 días hábiles",
                            "requisitos" => "Título de perpetuidad.",
                            "documentacion" => "Copia del título de perpetuidad.",
                            "pasos" => "1. Presentar título original. 2. Validación de datos. 3. Emisión de nueva carátula.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-file-alt"
                          ],
                          [
                            "nombre" => "Licencia para construcción de bóveda", 
                            "descripcion" => "Permiso necesario para construir una bóveda en el cementerio.",
                            "costo" => "$11.22",
                            "tiempo" => "2 días hábiles",
                            "requisitos" => "Título de perpetuidad.",
                            "documentacion" => "Entregar fotocopia del DUI y número del título de perpetuidad.",
                            "pasos" => "1. Aprobación del permiso.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-file-contract"
                          ]
                          ],

                "Trámites de Construcción" => [
                            [
                                "nombre" => "Licencia Habitacional",
                                "descripcion" => "Licencia de construcción para viviendas habitacionales.",
                                "costo" => "Según el monto presupuestado",
                                "tiempo" => "1 semana (siempre y cuando se presenten todos los requisitos requeridos el mismo día)",
                                "requisitos" => "Copia de escrituras del inmueble, Copia del DUI del propietario, Copia de NIT si es asociación o empresa.",
                                "documentacion" => "Presupuesto real de lo invertido en la construcción, Copia de planos en físico y formato digital, Factibilidades requeridas.",
                                "pasos" => "1. Presentar la documentación requerida. 2. Revisión por el técnico de la unidad. 3. Inspección por fiscalización municipal. 4. Elaboración de la licencia y cálculo. 5. Notificación del mandamiento de pago. 6. Recibir la licencia, presentando copia del recibo de cancelación.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-id-card"
                            ],
                            [
                                "nombre" => "Licencia Comercial",
                                "descripcion" => "Licencia de construcción para proyectos comerciales.",
                                "costo" => "Según el monto presupuestado",
                                "tiempo" => "1 semana (siempre y cuando se presenten todos los requisitos requeridos el mismo día)",
                                "requisitos" => "Copia de escrituras del inmueble, Copia del DUI del propietario, Copia de NIT si es asociación o empresa.",
                                "documentacion" => "Presupuesto real de lo invertido en la construcción, Copia de planos en físico y formato digital, Factibilidades requeridas.",
                                "pasos" => "1. Presentar la documentación requerida. 2. Revisión por el técnico de la unidad. 3. Inspección por fiscalización municipal. 4. Elaboración de la licencia y cálculo. 5. Notificación del mandamiento de pago. 6. Recibir la licencia, presentando copia del recibo de cancelación.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-building"
                            ]
                            ],
             "Contravencional" => [
                        [
                            "nombre" => "Procesos Administrativos Sancionatorios.",
                            "descripcion" => "Denuncias, avisos, y esquelas de desplazamiento impuestas por el CAM.",
                            "costo" => "Gratuito",
                            "tiempo" => "1 mes - 6 meses",
                            "requisitos" => "Con la resolución final emitida por el Delegado Contravencional quien mediante fundamentación legal y garantizándoles y protegiéndoles en todo momento los Derechos y garantías constitucionales que a todos los ciudadanos les asiste donde finalmente se absolverá o sancionara al infractor con amonestaciones verbales, reparación de daños, decomisos, trabajo de utilidad pública, multas, suspensiones de permisos y licencias y cierre definitivo de los negocios.",
                            "documentacion" => "1. Presentarse a la unidad con su Documento Único de Identidad o Pasaporte. 2. Las pruebas que presentan para probar y desvirtuar los hechos.",
                            "pasos" => "a) Que se sienta perjudicado por algún problema de convivencia ciudadana donde se encuentra regulado en la Ordenanza de Convivencia Ciudadana y Contravencional de esta Municipalidad, únicamente se procederá a abrir procesos de aquellas conductas que están descritas como contravenciones en esta Ordenanza las cuales ocasionan algún agravio al usuario que requiere de ese servicio público. b) Que nos proporcionen información para poder emplazar al infractor. c) Que citen con exactitud los hecho ocurridos y en qué lugar han sido ocurridos. d) Que exista disponibilidad de tiempo para asistir a las diferentes audiencias derivadas del proceso que se sigue, como colaborar en todo lo que se le requiera referente al caso que se sigue.",
                            "horario" => "Lunes a Viernes 8 AM - 4 PM",
                            "icono" => "fa-solid fa-gavel"
                        ]
                        ],
             "Mercado" => [
                    [
                        "nombre" => "Solvencia de impuesto de los mercados",
                        "descripcion" => "Obtención de constancia de puesto.",
                        "costo" => "$3.00",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Estar al día con los pagos de los impuestos de los mercados.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-alt"
                    ],
                    [
                        "nombre" => "Planes de pagos de los puestos que están en mora",
                        "descripcion" => "Opción para regularizar deudas de los puestos.",
                        "costo" => "Gratis",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Cancelar el 30% de la deuda total.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar nombre de locatario del puesto. 3) Estado de cuentas.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-credit-card"
                    ],
                    [
                        "nombre" => "Estado de cuentas",
                        "descripcion" => "Verificación del estado de cuentas de los puestos.",
                        "costo" => "Gratis",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-file-invoice"
                    ],
                    [
                        "nombre" => "Pago de impuestos de los mercados municipales",
                        "descripcion" => "Pago de impuestos correspondientes a los mercados.",
                        "costo" => "Lo define su tipo de impuesto",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Estar al día con los impuestos.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar tarjeta de cobro al día. 3) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-money-bill"
                    ],
                    [
                        "nombre" => "Trámites de licencia de comercialización",
                        "descripcion" => "Obtención o renovación de licencias de comercialización.",
                        "costo" => "$5.25, $10.50, $12.60 (dependiendo del trámite)",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Cancelar la licencia.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar recibo de cancelación de la licencia. 3) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-id-card"
                    ],
                    [
                        "nombre" => "Autorización de remodelación de puesto",
                        "descripcion" => "Solicitud para remodelar el puesto.",
                        "costo" => "Gratis",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Estar al día con sus impuestos de los mercados municipales.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar tarjeta de cobro al día. 3) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-tools"
                    ],
                    [
                        "nombre" => "Constancia de puesto",
                        "descripcion" => "Obtención de una constancia que acredita la ocupación del puesto.",
                        "costo" => "$3.00",
                        "tiempo" => "Máximo 10 minutos",
                        "requisitos" => "1) Estar inscritos en el sistema de SIMERCO. 2) Estar al día con los pagos de los impuestos de los mercados.",
                        "documentacion" => "1) Presentar el DUI. 2) Presentar nombre de locatario del puesto.",
                        "pasos" => "Presentarse en la Unidad de Mercados.",
                        "horario" => "Lunes a Viernes 8 AM - 4 PM",
                        "icono" => "fa-solid fa-certificate"
                    ]
                    ],

                    "Visto Bueno" => [
                     
                            [
                                "nombre" => "Matriculas",
                                "descripcion" => "Elaboración de Cartas de Venta",
                                "costo" => "$3.77 carta de venta",
                                "tiempo" => "10 a 15 minutos",
                                "requisitos" => "Reglamento para el uso de fierros o marcas de herrar ganado y traslado de semovientes art.73 y ordenanza municipal.",
                                "documentacion" => "DUI de comprador vigente, DUI de vendedor vigente, Matrícula de fierro vigente, DUI de comprador y vendedor, matrícula de fierro vigente, partida de nacimiento del comprador y 2 fotografías, para acta de traspaso de matrícula de fierro.",
                                "pasos" => "1. Presentar los documentos requeridos.\n2. Esperar la revisión de los documentos.\n3. Recibir la elaboración del documento solicitado.\n4. Recibir el mandamiento y/o recibo de cancelación.\n5. Firmar junto a la alcaldesa y el secretario o secretaria municipal.\n6. Recibir el documento final.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-file-alt"
                            ],
                            [
                                "nombre" => "Actas de Traspaso",
                                "descripcion" => "Elaboración de acta de traspaso de matrícula de fierro",
                                "costo" => "$21.17",
                                "tiempo" => "10 a 15 minutos",
                                "requisitos" => "Reglamento para el uso de fierros o marcas de herrar ganado y traslado de semovientes art.73 y ordenanza municipal.",
                                "documentacion" => "DUI de comprador vigente, DUI de vendedor vigente, Matrícula de fierro vigente, DUI de comprador y vendedor, matrícula de fierro vigente, partida de nacimiento del comprador y 2 fotografías.",
                                "pasos" => "1. Presentar los documentos requeridos.\n2. Esperar la revisión de los documentos.\n3. Recibir la elaboración del acta de traspaso.\n4. Firmar junto a la alcaldesa y el secretario o secretaria municipal.\n5. Recibir el documento final.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-file-alt"
                            ],
                            [
                                "nombre" => "Guías de Conducción",
                                "descripcion" => "Elaboración de guías de conducción de ganado a otras jurisdicciones",
                                "costo" => "$2.15 por cabeza",
                                "tiempo" => "10 a 15 minutos",
                                "requisitos" => "De conformidad al artículo 73, del Reglamento para el uso de fierro o marcas de herrar ganado y traslado de semovientes.",
                                "documentacion" => "DUI vigente del contribuyente que solicita la guía, DUI del conductor, matrícula de fierro del contribuyente que solicita la guía, tarjeta de circulación.",
                                "pasos" => "1. Presentar los documentos requeridos.\n2. Esperar la revisión de los documentos.\n3. Recibir la elaboración del documento solicitado.\n4. Recibir el mandamiento o recibo de pago.\n5. Firmar junto a la alcaldesa y el secretario municipal.\n6. Recibir el documento final.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-truck"
                            ],
                            [
                                "nombre" => "Cobros de Matriculas",
                                "descripcion" => "Cobro de matrícula de fierro de herrar ganado",
                                "costo" => "$21.17",
                                "tiempo" => "10 a 15 minutos",
                                "requisitos" => "Ordenanza de tasas municipales, art. 21, publicada en el diario oficial Tomo No 424, PÁGINA 70, NÚMERO: 135 fecha 22 de julio de 2019.",
                                "documentacion" => "Matrícula de fierro.",
                                "pasos" => "1. Presentar la matrícula de fierro.\n2. Esperar la revisión de la matrícula.\n3. Verificar en el libro de anotaciones.\n4. Recibir el mandamiento o recibo.\n5. Pagar en caja.\n6. Recibir el recibo final.",
                                "horario" => "Lunes a Viernes 8 AM - 4 PM",
                                "icono" => "fa-solid fa-money-bill"
                            ]
                        ]
                        
            ];

      
        ?>
    
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<div class="project-two__filter-box">
    <ul class="project-two__filter style1 post-filter list-unstyled clearfix">
        <li data-filter="todas" class="active">
            <span class="filter-text" onclick="mostrarCategoria('todas')">Todo</span>
        </li>
        <?php foreach ($tramites as $categoria => $servicios): ?>
            <li data-filter="<?= strtolower(str_replace(' ', '-', $categoria)) ?>">
                <span class="filter-text" onclick="mostrarCategoria('<?= strtolower(str_replace(' ', '-', $categoria)) ?>')">
                    <?= htmlspecialchars($categoria) ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Contenido de los Trámites -->
<div class="tab-content" id="tramitesTabsContent">
    <?php foreach ($tramites as $categoria => $servicios): ?>
        <div class="tab-pane fade show active filter-item <?= strtolower(str_replace(' ', '-', $categoria)) ?>" data-categoria="<?= strtolower(str_replace(' ', '-', $categoria)) ?>">
            <div class="container mt-4">
                <div class="row">
                    <?php foreach ($servicios as $servicio): ?>
                        <div class="col-xl-4 col-lg-6">
                            <div class="services-two__single">
                                <div class="services-two__icon">
                                    <span class="<?= htmlspecialchars($servicio['icono']) ?>"></span>
                                </div>
                                <h3 class="services-two__title">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalInfo" 
                                       onclick="mostrarInfo(
                                           '<?= htmlspecialchars($servicio['nombre']) ?>',
                                           '<?= htmlspecialchars($servicio['descripcion']) ?>',
                                           '<?= htmlspecialchars($servicio['costo']) ?>',
                                           '<?= htmlspecialchars($servicio['tiempo']) ?>',
                                           '<?= htmlspecialchars($servicio['requisitos']) ?>',
                                           '<?= htmlspecialchars($servicio['documentacion']) ?>',
                                           '<?= htmlspecialchars($servicio['pasos']) ?>',
                                           '<?= htmlspecialchars($servicio['horario']) ?>'
                                       )">
                                        <?= htmlspecialchars($servicio['nombre']) ?>
                                    </a>
                                </h3>
                                <p class="services-two__text">
                                    <?= htmlspecialchars($servicio['descripcion']) ?>
                                </p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalInfo" 
                                   onclick="mostrarInfo(
                                       '<?= htmlspecialchars($servicio['nombre']) ?>',
                                       '<?= htmlspecialchars($servicio['descripcion']) ?>',
                                       '<?= htmlspecialchars($servicio['costo']) ?>',
                                       '<?= htmlspecialchars($servicio['tiempo']) ?>',
                                       '<?= htmlspecialchars($servicio['requisitos']) ?>',
                                       '<?= htmlspecialchars($servicio['documentacion']) ?>',
                                       '<?= htmlspecialchars($servicio['pasos']) ?>',
                                       '<?= htmlspecialchars($servicio['horario']) ?>'
                                   )" class="services-two__learn-more">
                                   <strong>Más Información</strong> <span class="icon-arrow-right"></span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal de Información del Trámite -->
<div class="modal fade" id="modalInfo" tabindex="-1" aria-labelledby="modalInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Aquí hemos cambiado modal-lg por modal-xl -->
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalInfoLabel" style="font-weight: 900;">Detalles del Trámite</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h5 id="modalNombre"></h5>
                <p><strong>Descripcion:</strong> <span id="modalDescripcion"></span></p>
                <p><strong>Costo:</strong> <span id="modalCosto"></span></p>
                <p><strong>Tiempo de Procesamiento:</strong> <span id="modalTiempo"></span></p>
                <p><strong>Requisitos:</strong> <span id="modalRequisitos"></span></p>
                <p><strong>Documentación Necesaria:</strong> <span id="modalDocumentacion"></span></p>
                <p><strong>Pasos:</strong> <span id="modalPasos"></span></p>
                <p><strong>Horario de Atención:</strong> <span id="modalHorario"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para Filtrar Categorías y Mostrar Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function mostrarCategoria(categoria) {
        let elementos = document.querySelectorAll('.filter-item');
        elementos.forEach(el => {
            if (categoria === 'todas') {
                el.style.display = 'block';
            } else {
                if (el.classList.contains(categoria)) {
                    el.style.display = 'block';
                } else {
                    el.style.display = 'none';
                }
            }
        });
    }

    // Función para mostrar los detalles en el modal
    function mostrarInfo(nombre, descripcion, costo, tiempo, requisitos, documentacion, pasos, horario) {
        document.getElementById('modalNombre').innerText = nombre;
        document.getElementById('modalDescripcion').innerText = descripcion;
        document.getElementById('modalCosto').innerText = costo;
        document.getElementById('modalTiempo').innerText = tiempo;
        document.getElementById('modalRequisitos').innerText = requisitos;
        document.getElementById('modalDocumentacion').innerText = documentacion;
        document.getElementById('modalPasos').innerText = pasos;
        document.getElementById('modalHorario').innerText = horario;
    }

    // Hacer que todos los trámites sean visibles al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        mostrarCategoria('todas');
    });
</script>

 <br> <br> <br> <br> <br> <br> <br> 

        <?php require_once('parts/home/cta.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 
        <?php require_once('parts/footer/footer.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 