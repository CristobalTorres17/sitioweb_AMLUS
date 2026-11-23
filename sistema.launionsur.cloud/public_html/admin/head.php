<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/colorpicker/colorpicker.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/imgareaselect/css/imgareaselect-default.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/select2/select2.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/jgrowl/jquery.jgrowl.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/fonts/ptsans/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/fonts/icomoon/style.css" media="screen">

<link rel="stylesheet" type="text/css" href="../biblioteca/css/mws-style.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/icons/icol16.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/icons/icol32.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/demo.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/jui/css/jquery.ui.all.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/jui/css/jquery.ui.timepicker.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/jui/jquery-ui.custom.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/mws-theme.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/themer.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/diseno.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/css/form.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/custom-plugins/picklist/picklist.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/ibutton/jquery.ibutton.css" media="screen">
<link rel="stylesheet" type="text/css" href="../biblioteca/plugins/cleditor/jquery.cleditor.css" media="screen">	
	
	
	
	
	
</head>
	<script type="text/jscript">
	function Resaltar_On(GridView)
{
    if(GridView != null)
    {
    GridView.originalBgColor = GridView.style.backgroundColor;
    GridView.style.backgroundColor='#88A9EB';
    GridView.style.cursor = 'hand'; 
    }
}

function Resaltar_Off(GridView)
{
    if(GridView != null)
    {
    GridView.style.backgroundColor = GridView.originalBgColor;    
    }
}
function agregar(URL,ancho,alto){
		var posicion_x;
		var posicion_y;
	posicion_x=(screen.width/2)-(ancho/2);
	posicion_y=(screen.height/2)-(ancho/2);	
window.open(URL,"agregar","width="+ancho+",height="+alto+",menubar=no,scrollbars=no,toolbar=no,directories=no,resizable=no,top="+posicion_y+",left="+posicion_x+"");
}
function editar(URL,ancho,alto){
		var posicion_x;
		var posicion_y;
	posicion_x=(screen.width/2)-(ancho/2);
	posicion_y=(screen.height/2)-(ancho/2);	
window.open(URL,"editar","width="+ancho+",height="+alto+",menubar=no,scrollbars=no,toolbar=no,directories=no,resizable=no,top="+posicion_y+",left="+posicion_x+"");
}
function confirmar(url){ 
if (!confirm("¿Está seguro de que desea eliminar el registro?")) { 
return false; 
} 
else { 
document.location= url; 
return true; 
} 
} 	
	</script>