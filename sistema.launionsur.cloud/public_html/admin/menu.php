<ul>		
<li class="active"><a href="iniciar.php"><i class="icon-home"></i> Inicio</a></li>
<li><a href="#"><i class="icon-edit"></i>Principal</a>
<ul>
<!--<li><a href="banner/banner_ver.php" target="info">Banner</a></li>-->
<li><a href="vision/vision_ver.php" target="info">Vision</a></li>
<li><a href="mision/mision_ver.php" target="info">Mision</a></li>
<li><a href="historia/historia_ver.php" target="info">Historias</a></li>
<li><a href="valores/valores_ver.php" target="info">Valores</a></li>
</ul>
</li>
<li><a href="eventos/ver_evento.php" target="info"><i class="icon-newspaper"></i> Eventos y Actos</a></i>
<!--Administracion de Noticias--
<li class="active"><a href="#"><i class="icon-newspaper"></i> Administracion de Noticias</a>
<ul>
<?php //if($_SESSION["rol"]== "super" or $_SESSION["rol"] == "Super") {?>
<li><a href="categori-noti/catego-noti_ver.php" target="info"><i class="icon-table"></i> Categorias</a></li>
<?php //}?> 
<li><a href="noticias/noticia_ver.php" target="info"><i class="icon-book"></i> Noticias</a></li>

 <?php //if($_SESSION["rol"] == "super" or $_SESSION["rol"] == "Super" or $_SESSION["rol"] == "Administrador" or $_SESSION["rol"] == "administrador")  { ?>
<li><a href="comentarios/comentario_ver.php" target="info"><i class="icon-check"></i> Comentarios</a></li>
 <?php //}?> 
</ul>
</li>-->
 <?php if($_SESSION["rol"] == "super" or $_SESSION["rol"] == "Super")  { ?>
<!--Portal de Transparencia-->
<li><a href="#"><i class="icon-list-2"></i> Portal de Transparencia</a>
<ul>
<li><a href="menu/menu_ver.php" target="info">Menu</a></li>
<li><a href="sub-menu/submenu_ver.php" target="info">Sub Menu</a></li>
<li><a href="carpeta/carpeta_ver.php" target="info">Carpetas</a></li>
<li><a href="nivel/nivel_ver.php" target="info">Niveles</a></li>
<li><a href="sub-nivel/subNivel_ver.php" target="info">Sub Niveles</a></li>
<li><a href="annos/annos_ver.php" target="info">Años</a></li>
<li><a href="archivos/archivo_ver.php" target="info">Archivos</a></li>
<li><a href="usuarios/usu_ver.php" target="info">usu</a></li>    
</ul>
</li>
<?php }?>
<!--Multimedia
<li class="active"><a href="#"><i class="icon-pictures"></i>Multimedia</a>
<ul>
<li><a href="imagenes/ver_album.php" target="info"><i class="icon-picture"></i> Imagenes</a></li>
<li><a href="videos/ver_album.php" target="info"><i class="icon-film"></i> Videos</a></li>
</ul>
</li>-->
</ul>