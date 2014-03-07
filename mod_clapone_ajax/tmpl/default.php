<?php

define('_JEXEC', 1);
//here jpath_base changes when loaded through ajax..(it wont be thru index.php then

if (!defined('_JDEFINES')) {
	if(isset($_GET["start"]))	{ define('JPATH_BASE', '../../..');  }
	else {  define('JPATH_BASE', dirname(__FILE__));  }
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';
$mainframe =& JFactory::getApplication('site');
// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;
$start = JRequest::getVar('start','0', 'get');
$world1=1;$world2=100;
if($_COOKIE["world"]=="tollywood")
{$world1=4;$world2=5;
}
if($_COOKIE["world"]=="bollywood")
{$world1=6;$world2=7;
}

$number=15;
$next_start=$start+$number;
$base_url=JURI::base().'../../../';
if($start==0)
{
$number=20;
$base_url=JURI::base();
 echo ' 	
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>';
echo '<script src="'.JURI::base().'new/cap/jquery.masonry.min.js"></script>';
echo '<link rel="stylesheet" href="'.JURI::base().'/new/cap/css/style.css" />';
echo '<style>
#facepile1 h31 {
background: #000;
display: block;
color: white;

opacity: .70;
  width:200px;
font-weight: 400;
text-align: center;
line-height: auto;
padding: 5px 0;
margin-top: 0px;
min-width: 200px;
position: absolute;
z-index: 99;
/*margin-top:-52px;*/
bottom:10px;
}

.box .upperwrapper{
background: #000;
opacity:0.7;
visibility:hidden;
//left: 14px;
position: absolute;
//top: 14px;
width: 200px;;
z-index: 101;
}
.box:hover .upperwrapper{
visibility:visible;
}
.box-img:hover
{
background: none repeat scroll 0% 0% rgba(255, 255, 255, 0.1);
}

</style>';
}
$views=array();
$db = JFactory::getDBO();
$db->getQuery($query);
$query = "SELECT a.virtuemart_product_id,file_url,e.slug,e.virtuemart_manufacturer_id,e.mf_name,z.hits,z.product_sales
FROM `nyhar_virtuemart_product_medias` a
join nyhar_virtuemart_products z
	     on  z.virtuemart_product_id=a.virtuemart_product_id and z.published=1
join nyhar_virtuemart_product_categories b 
             on b.virtuemart_category_id=1 and a.virtuemart_product_id=b.virtuemart_product_id  
join nyhar_virtuemart_medias c 
             on a.virtuemart_media_id=c.virtuemart_media_id
join nyhar_virtuemart_product_manufacturers d
 	     on d.virtuemart_product_id=a.virtuemart_product_id
join nyhar_virtuemart_manufacturers_en_gb e
	on e.virtuemart_manufacturer_id=d.virtuemart_manufacturer_id
join nyhar_virtuemart_manufacturers f
        on e.virtuemart_manufacturer_id=f.virtuemart_manufacturer_id and (f.virtuemart_manufacturercategories_id>=".$world1." and f.virtuemart_manufacturercategories_id<=".$world2." )
order by md5(a.virtuemart_product_id) desc
LIMIT ".$start.",".$number."
;";
$db->setQuery($query);
$array=$db->loadObjectList();
shuffle($array);
//follow-get visitor id,his following from session email####################

$my_session =JFactory::getSession();
$user_email=$my_session->get('email');
//echo $user_email;
$db = JFactory::getDBO();
$query = $db->getQuery(true);
//get visitor id from email
$query->select('id');
$query->from('#__visitors');
$query->where('email ="'.$user_email.'"' );
$db->setQuery($query);
$visitor_id= $db->loadResult();

//get whom he is following
$query2 = $db->getQuery(true);
$query2->select('actor_id');
$query2->from('#__follow');
$query2->where('visitor_id='.$visitor_id);
$db->setQuery($query2);
$rak=$db->loadResultArray();
//print_r($rak);
$follow=array();
for ($i=0;$i<count($rak);$i++)
{
        $follow[$rak[$i]]=1;
}
//no of followers
$query2 = $db->getQuery(true);
$query2->select('actor_id,count(visitor_id)');
$query2->from('#__follow');
$query2->group('actor_id');
$db->setQuery($query2);
$rak=$db->loadRowList();
//print_r($rak);
$no_follow=array();
for ($i=0;$i<count($rak);$i++)
{
//	echo "<br />".$rak[$i][0]." ".$rak[$i][1]."<br />";
	$no_follow[$rak[$i][0]]=$rak[$i][1];
}
//number of followers-end db

//movie
foreach ($array as $arr)
{
	array_push($views,$arr->virtuemart_product_id);
}
$db = JFactory::getDBO();
$db->getQuery($query);
$query = "SELECT b.category_name,a.`virtuemart_product_id` FROM `nyhar_virtuemart_product_categories` a join
nyhar_virtuemart_categories_en_gb b on b.`virtuemart_category_id`=a.`virtuemart_category_id` and b.`virtuemart_category_id`!=1 where `virtuemart_product_id` IN(".implode(',',$views).')'."group by a.`virtuemart_product_id`
;";
$db->setQuery($query);
$array2=$db->loadRowList();

for ($i=0;$i<count($array2);$i++)
{
	$movie[$array2[$i][1]]=$array2[$i][0];
}
//print_r($movie);
//end movie
echo "<script type='text/javascript'> visitor_id=".$visitor_id.";</script>" ;
echo "<script> base_url='".$base_url."';</script>";

?>
<script>
$.ajaxSetup ({  
        cache: false  
    });
  function actorfollow(actor_id)
{

if(typeof visitor_id === 'undefined'){  $("#resultfollow").load(base_url+'/totalpopup.php',"act="+actor_id);  return;}
var temp_followno=$("."+"follow"+actor_id).text();
temp_followno=parseInt(temp_followno,10)+1;
//temp_followno=("000000"+temp_followno).slice(-6);
  $("."+"follow"+actor_id).text(temp_followno+" followers");
$("."+actor_id).children('.follow').css("display","none");
$("."+actor_id).children('.unfollow').css("display","inline");
  $("#result2").load(base_url+'/follow.php','type=1&actor_id='+actor_id+'&visitor_id='+visitor_id, function(responseText){  

    });
};
</script>
<div id="resultfollow" class="resultfollow" style="z-index:99999;" ></div>
<div id="result2"></div>
<?php
//##########################
//echo "<script> window.start=".$next_start." </script>"."a";
echo "<div id='container1' style='margin:0px auto; margin-top: -50px;'>";
foreach ($array as $arr)
	{
		$manufacturer_id=$arr->virtuemart_manufacturer_id;

//follow,followers
      
        
$link=$base_url."actors/manufacturer/".$arr->slug."?specific_id=".$arr->virtuemart_product_id;
		echo "<div  class='box col3' style='width:200px;'>";
		
		//wrapper
  echo '<div class="upperwrapper"> ';
  echo '<div style="padding:5px;">';
  //follow button
        echo "<div style='display:inline' class=".$manufacturer_id."  >";
	if($follow[$manufacturer_id]!=1) { $to_be_fol="";$already_fol="display:none;";}
	else { $to_be_fol="display:none;";$already_fol="";}
                
                        echo '<div class="follow" style="display:inline;'.$to_be_fol.'"><input type="button" onClick="actorfollow('.$manufacturer_id.');" value="Follow" /></div>';
                        echo '<div class="unfollow" style="display:inline;'.$already_fol.'"><input type="button" onClick="" value="Following" /></div>';
                
                echo '</div>';
                //end of follow button
                	//followers
	echo "<div style='display:inline;float:right;margin-right:5px;color:white;' class="."follow".$manufacturer_id.">";
		echo $no_follow[$manufacturer_id]." followers";
                
		echo "</div>";
		//end of followers
	
        echo '</div></div>';
        //end wrapper
        echo "<a href='".$link."'> <img class='box-img' src='".$base_url.$arr->file_url."'/></a>";

		echo '<div id="facepile1"><h31 style="float:right">  <div>';
	echo '<table width="100%" style="padding:0px;margin-top:0px;background:rgb(17,17,17)"><tr>';
	echo "<td style='width:50%'>"." 
	<span class='icon' style='display: inline-block;
background-repeat: no-repeat;
background-position: 0px 0px;
margin-right: 5px;
vertical-align: middle;background-image: url(".$base_url."images/icon_quickview.png);
width: 19px;
height: 13px;'></span>".$arr->hits."
	</td>";
	echo "<td style='width:50%'> <span style='display:inline-block;vertical-align:middle;width: 18px;
height: 16px;background: url(\"http://localhost/w/images/social-icons.png\")  no-repeat scroll 0px -16px ; margin-right: 6px;'></span>".$arr->product_sales." </td></tr>";
	echo '</table>';
                echo '<font size="3px">'.$arr->mf_name." in ".$movie[$arr->virtuemart_product_id].'</font> </h3></div>';
                echo "</div>";
	
echo " </div>";
	}
echo "</div>";
$db = JFactory::getDBO();
$db->getQuery($query);
$query = "UPDATE nyhar_virtuemart_products set `hits`=`hits`+1 where virtuemart_product_id IN(".implode(',',$views).')';
$db->setQuery($query);
$array=$db->loadObjectList();

if($start==0)
{
echo "<div id='loading' style='margin-bottom:15px;'><center> <img src='".JURI::base()."images/loading.gif' width='50px' height='50px' /></center></div>";
echo "<script>base_url='".JURI::base()."';</script>";
echo "<script> window.next=20;window.items_got=1;window.loading=1;
$(window).load(function(){ 
    
    $('#container1').masonry({
      itemSelector: '.box',
      columnWidth: 2,
      isAnimated: false,
      isFitWidth: true

    });
    
    window.loading=0;

  });
  
  $(window).scroll(function()
{
    if($(window).scrollTop() > $(document).height() - $(window).height() -750 && window.items_got>0 && window.loading<1)
    {
        window.loading=1;
	$.ajax({
	type:'GET',
        cache:false,
        data:{ start : window.next },  
    url: base_url+'modules/mod_clapone_ajax/tmpl/default.php',
    success: function(data) {
                 data=$(data).find('div.box');
        window.items_got=data.size();
        data.css({display:'none'});
        $('#container1').append(data);
         $('#container1').imagesLoaded(function() {  
                 data.css({display:''});
            $('#container1').masonry('appended', data ,true,function(){
    window.loading=0;});
            window.next+=15;
		if(window.items_got<1){\$('#loading').hide();}
     });
     }
    });
   
    }
    
    });
</script>";
}
?>


