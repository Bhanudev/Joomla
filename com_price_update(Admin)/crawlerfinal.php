<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByName('id[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
if (file_exists(dirname(__FILE__) . '/defines.php')) {
	include_once dirname(__FILE__) . '/defines.php';
}
if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(__FILE__));
	require_once JPATH_BASE.'/includes/defines.php';
}
require_once JPATH_BASE.'/includes/framework.php';
// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;
// Instantiate the application.$pp = JFactory::getApplication('site');
//echo "echo ehco";

$db = JFactory::getDbo();
$query1 = $db->getQuery(true);
$query1->select('virtuemart_custom_id');
$query1->from('nyhar_virtuemart_customs');
$query1->where('custom_title="link"');
$db->setQuery($query1);
$rr = $db->LoadResult(); // we will get id of the link in the custom fields

$query2 = $db->getQuery(true);
$query2->select('virtuemart_product_id,custom_value');
$query2->from('nyhar_virtuemart_product_customfields');
$query2->where('virtuemart_custom_id='.$rr); 
$db->setQuery($query2);
$row2 = $db->loadRowList(); // gets all the links and ids of the products

//opening files for error handlers

$from = "";
$from = JRequest::getVar('from','', 'get');

$to = "";
$to = JRequest::getVar('to','', 'get');
$movies = "";
$movies = JRequest::getVar('movies','', 'get');
//echo count($row2)."  and we are looking at indexes ".$movies." from ".$from." to ".$to;

$handle1 = fopen("nolinkpart1", "w");
$handle2 = fopen("linknotcrawledpart1", "w");
$handle3 = fopen("linknothandledpart1", "w");
$handle4 = fopen("resultpart1", "w");
$handle5 = fopen("soldout1", "w");
$eee = count($row2);


//arrays for storing errors
$i1=0;$i2=0;$i3=0;$i4=0;$no_items=0;$no_errors=0;


// $eee  = 1;
for($j=0;$j < $eee;$j++){

// if condition for the movie
if($movies != "")
{


//echo "movies is received <br />";
//echo "<br />id is".$row2[$j][0]."<br />";
$query3 = $db->getQuery(true);
$query3->select('virtuemart_category_id');
$query3->from('nyhar_virtuemart_product_categories');
$query3->where('virtuemart_product_id='.$row2[$j][0]); 
$db->setQuery($query3);
$catid = $db->loadResult();



//echo "catid = ".$catid;

$query4 = $db->getQuery(true);
$query4->select('category_name');
$query4->from('nyhar_virtuemart_categories_en_gb');
$query4->where('virtuemart_category_id='.$catid); 
$db->setQuery($query4);
$catname = $db->loadResult();

//echo "catname = ".$catname;

if(strncasecmp($catname, $movies, strlen($movies)))
{
//echo "catname = ".$catname."<br />";
continue;
}
   
   else
   { 
   //echo "catname = ".$catname;
 // echo "A product of given movie with id ". $row2[$j][0] ."and link " .$row2[$j][1]. "is found <br/>"; 
   $no_items++;
   }
}

else if($from != "" && $to != "")
{

   if($row2[$j][0] < $from || $row2[$j][0] > $to)
   {
      echo "no<br />";
      continue;
   }
   else
   { echo "yes<br />";
   $no_items++;
   }
}

else { $no_items++;}



$add = "";
//echo (int)$j."\n";
$link = $row2[$j][1];


if(strlen($link) == 0)
{
    fwrite($handle1,"link  doesnt exists for id   ".$row2[$j][0]. "\n");
    $nolink[$i1][0]=$row2[$j][0];
    $nolink[$i1][1]=$row2[$j][1];
    $i1++;
    continue;
}
$price = "Sold Out";
$html = "";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$html =  curl_exec($ch); 
curl_close ($ch);
if(strlen($html) == 0)
{
    fwrite($handle2,"link  didnot get crawled for id   ".$row2[$j][0]."  link is ".$row2[$j][1]. "\n");
    $linknotcrawled[$i2][0]=$row2[$j][0];
    $linknotcrawled[$i2][1]=$row2[$j][1];
    $i2++;
    continue;
}


if(strpos($link,'flipkart')==true){
$add = "?affid=claponebuy";

if(strpos($link,$add) == false)
{
      $link = $link.$add;
}

preg_match ( '<.*font-final.*>', $html, $pieces);  // flipkart,If the link is from flipkart then we get the rate using regular expression 
$e = trim($pieces[0]); 
$priceline=explode(". ",$e);
if(count($priceline)==2){
	$price=explode("<",$priceline[1]);
	$price=$price[0];
	for($i=0;$i<count($price);$i++){
		if($price[$i]>'9' and $price[$i]<'0'){
			echo "Error occuered here\n";
		}
	}
}
	if( strpos($html,"This item is Permanently discontinued") ==true || strpos($html,"notify_message") ==true)
	{
		$price = "Sold Out";
                fwrite($handle5,"Product is sold out for flipkart  id ".$row2[$j][0]. "\n");
	        $soldout[$i4][0]=$row2[$j][0];
                $soldout[$i4][1]=$row2[$j][1];
                $i4++;continue;
        }

}

else if(strpos($link,'jabong')==true){

preg_match ( '(productPriceGaq.*)', $html, $pieces); // jabong,If the link is from jabong then we get the rate using regular expression 

//print_r($pieces);

//$e = trim($pieces[0]); 

//echo $e;

$priceline=explode("'",$pieces[0]);

//print_r($priceline);

if(count($priceline)>1){
//	echo $priceline[1] . "\n";
	$price=$priceline[1];
	for($i=0;$i<count($price);$i++){
		if($price[$i]>'9' and $price[$i]<'0'){
			echo "Error occuered here\n";
		}
	}
//echo $price;
}
	if(strpos($html,"sold-out-badge") ==true)
	{
		$price = "Sold Out";
                fwrite($handle5,"Product is sold out for jabong  id   ".$row2[$j][0]. "\n");
                $soldout[$i4][0]=$row2[$j][0];
                $soldout[$i4][1]=$row2[$j][1];
                $i4++;continue;
	}
}

else if(strpos($link,'fashionara')==true){
$add = '?utm_source=clapOne&utm_medium=affliate&utm_campaign=clap1onsite';

if(strpos($link,$add) == false)
{
      $link = $link.$add;
}

preg_match ( '(productPrice.*,)', $html, $pieces); // jabong,If the link is from jabong then we get the rate using regular expression 
if(count($pieces)>0){
$priceline=explode(",",$pieces[0]);
}
if(count($priceline)>0){
//	echo $priceline[0] . "\n";
	$price=explode(":",$priceline[0]);
	$price=$price[1];
	for($i=0;$i<count($price);$i++){
		if($price[$i]>'9' and $price[$i]<'0'){
			echo "Error occuered here\n";
		}
	}
}
	if(strpos($html,"out-of-stock") ==true)
	{
		$price = "Sold Out";
                fwrite($handle5,"Product is sold out for fashionara  id   ".$row2[$j][0]. "\n");
                $soldout[$i4][0]=$row2[$j][0];
                $soldout[$i4][1]=$row2[$j][1];
                $i4++;continue;
	}
}

else if(strpos($link,'myntra')==true){

preg_match ( '(meta.*itemprop.*price.*)', $html, $pieces); // jabong,If the link is from jabong then we get the rate using regular expression 

//print_r($pieces);

$e = trim($pieces[0]); 

//echo $e;

$priceline=explode('content=',$e);

//print_r($priceline);

if(count($priceline)>1){
//	echo $priceline[1] . "\n";
	$price=explode('"',$priceline[1]);
	$price=$price[1];
	$out = "";
	for($i=0;$i<strlen($price);$i++){
		
//		print $price[$i] . "\n";
		$oc=ord($price[$i]);
		if(($oc<ord('0') or $oc>ord('9')) and $oc!=ord(',')){
			echo "Error occuered here\n";
		}
		if($price[$i] != ','){
			$out =$out.$price[$i];
		}
	}
	//echo $out."<<<<";
}
	$price = $out;
	if(strpos($html,"mk-sold-out") ==true)
	{
		$price = "Sold Out";
                fwrite($handle5,"Product is sold out for myntra  id   ".$row2[$j][0]. "\n");
                $soldout[$i4][0]=$row2[$j][0];
                $soldout[$i4][1]=$row2[$j][1];
                $i4++;continue;
	}
}

else if(strpos($link,'utsavfashion')==true){

preg_match ( '(itempricediv.*</b>)', $html, $pieces); // jabong,If the link is from jabong then we get the rate using regular expression 

//print_r($pieces);

$e = trim($pieces[0]); 

//echo $e;

$priceline=explode('b>',$e);

//print_r($priceline);

if(count($priceline)>1){
	$spriceline=explode('<',$priceline[1]);
//	print_r($spriceline);
	if(count($spriceline)>1){
		$sspriceline=explode('>',$spriceline[1]);
//		print_r($sspriceline);
	}
	if(count($sspriceline)>0)
		$price=trim($sspriceline[1]);
	
	for($i=0;$i<strlen($price);$i++){
//		print $price[$i] . "\n";
		$oc=ord($price[$i]);
		if(($oc<ord('0') or $oc>ord('9')) and $oc!=ord(',')){
			echo "Error occuered here\n";
		}

		}
	}
}
else if(strpos($link,'koovs')==true){
preg_match ( '(selling_price.*")', $html, $pieces); // jabong,If the link is from jabong then we get the rate using regular expression 

//print_r ($pieces);

if(count($pieces)>0){
$priceline=explode(",",$pieces[0]);
}

//print_r($priceline);

if(count($priceline)>0){
//	echo $priceline[0] . "\n";
	$price=explode('"',$priceline[0]);
	$price=trim($price[2]);
	for($i=0;$i<count($price);$i++){
		if($price[$i]>'9' and $price[$i]<'0'){
			echo "Error occuered here\n";
		}
	}
}
}
else if(strpos($link,'yepme')==true){
}
if($price == "Sold Out")
{
    fwrite($handle3,"link was not handled id   ".$row2[$j][0]."  link is ".$row2[$j][1]. "\n");
    $linknothandled[$i3][0]=$row2[$j][0];
    $linknothandled[$i3][1]=$row2[$j][1];
    $i3++;continue;
}

//echo $link."<br />";
//echo $price."<br /><br />";

try{
$db =& JFactory::getDBO();
$query = "UPDATE #__virtuemart_product_customfields SET custom_price =". $price." WHERE virtuemart_product_id=".$row2[$j][0]." AND "."virtuemart_custom_id=".$rr;
$db->setQuery($query);
$db->query();

$query1 = "UPDATE #__virtuemart_product_customfields SET custom_vale =". $link." WHERE virtuemart_product_id=".$row2[$j][0]." AND "."virtuemart_custom_id=".$rr;
$db->setQuery($query1);
$db->query();   
   
}
catch(Exception $e)
{
    echo $e->getMessage();
    
}

fwrite($handle4,$j."\n"."link  is  ".$link."  id ".$row2[$j][0]. "\n".$price."\n\n");
}

fclose($handle1);
fclose($handle2);
fclose($handle3);
fclose($handle4);
fclose($handle5);
echo "<br />";	
$no_errors=count($nolink)+count($linknotcrawled)+count($linknothandled)+count($soldout);

echo "Totally there are ".$no_items." products<br />";
echo "Totally there are ".$no_errors."  	errors <br />";
echo "Of them: ".count($nolink)." no link errors ".count($linknotcrawled)." link not crawled errors ".count($linknothandled)." link not handled errors ".count($soldout)." sold out errors <br />";

echo '<form action="none.php" method="get">';
echo "NO link Errors: <br />";
for($i=0;$i<count($nolink);$i++)
{
echo '<input type="checkbox" name="id[]" value="'.$nolink[$i][0].'">';
echo "ID is ".$nolink[$i][0]." and link is <a href='".$nolink[$i][1]."' target='_blank'>".$nolink[$i][1]."</a><br />";
}

echo "link not crawled errors: <br />";
for($i=0;$i<count($linknotcrawled);$i++)
{
echo '<input type="checkbox" name="id[]" value="'.$linknotcrawled[$i][0].'">';
echo "ID is ".$linknotcrawled[$i][0]." and link is <a href='".$linknotcrawled[$i][1]."' target='_blank'>".$linknotcrawled[$i][1]."</a><br />";
}

echo "link not handled errors: <br />";
for($i=0;$i<count($linknothandled);$i++)
{
echo '<input type="checkbox" name="id[]" value="'.$linknothandled[$i][0].'">';
echo "ID is ".$linknothandled[$i][0]." and link is <a href='".$linknothandled[$i][1]."' target='_blank'>".$linknothandled[$i][1]."</a><br />";
}

echo "sold out errors: <br />";
for($i=0;$i<count($soldout);$i++)
{
echo '<input type="checkbox" name="id[]" value="'.$soldout[$i][0].'">';
echo "ID is ".$soldout[$i][0]." and link is <a href='".$soldout[$i][1]."' target='_blank'>".$soldout[$i][1]."</a><br />";
}



?>
<input type="checkbox" onClick="toggle(this)" /> Toggle All<br/>
<input type="submit" name="type" value="Unpublish"/>
<input type="submit" name="type" value="Delete"/>
</form>





