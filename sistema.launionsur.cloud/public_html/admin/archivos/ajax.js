function load(str)
{

var xmlhttp;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
     var sel = document.getElementById("v1");
     sel.remove(sel.selectedIndex);

     var sel2 = document.getElementById("v2");
     sel2.remove(sel2.selectedIndex);

     var sel3 = document.getElementById("v3");
     sel3.remove(sel3.selectedIndex);

     var sel4 = document.getElementById("v4");
     sel4.remove(sel4.selectedIndex);
     
  }

xmlhttp.open("POST","selec_ajax.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);


}



function load2(str)
{
var xmlhttp;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv2").innerHTML=xmlhttp.responseText;
    }

     var sel2 = document.getElementById("v2");
     sel2.remove(sel2.selectedIndex);

     var sel3 = document.getElementById("v3");
     sel3.remove(sel3.selectedIndex);

     var sel4 = document.getElementById("v4");
     sel4.remove(sel4.selectedIndex);

  }
xmlhttp.open("POST","selec_ajax2.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("r="+str);
}



function load3(str)
{
var xmlhttp;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv3").innerHTML=xmlhttp.responseText;
    }
    
       var sel3 = document.getElementById("v3");
     sel3.remove(sel3.selectedIndex);

     var sel4 = document.getElementById("v4");
     sel4.remove(sel4.selectedIndex);

  }
xmlhttp.open("POST","selec_ajax3.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("t="+str);
}





function load4(str)
{
var xmlhttp;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv4").innerHTML=xmlhttp.responseText;
    }

      var sel4 = document.getElementById("v4");
     sel4.remove(sel4.selectedIndex);

  }
xmlhttp.open("POST","selec_ajax4.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("Y="+str);
}




function load5(str)
{
var xmlhttp;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv5").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("POST","selec_ajax5.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("u="+str);
}
