<?php
$paging->mostrarPrimera("|<", true); 
$paging->mostrarUltima(">|", true); 
$paging->mostrarAnterior(false);
$paging->mostrarSiguiente(false); 
$paging->linkClase('nav'); 
$paging->mostrarActual("<span class=\"navthis\">{n}</span>"); 
$paging->agregarConsulta($q); 
$paging->modo('desarrollo');
$paging->ejecutar();
?>