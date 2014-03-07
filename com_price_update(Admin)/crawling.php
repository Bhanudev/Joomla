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

// Instantiate the application.
$app = JFactory::getApplication('site');

	JToolBarHelper::title(JText::_('Price Update Tool'), 'search.png');
	
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

$query3 = $db->getQuery(true);
$query3->select('title');
$query3->from(' nyhar_k2_items');
//$query3->where('published=1');
$db->setQuery($query3);
$rak=$db->loadResultArray();
$cou = count($rak);

echo count($row2)."  these are the total number of links";
echo "the starting and ending product id's are ".$row2[1][0]." ".$row2[count($row2)-1][0];


echo'
<script>
.ajaxSetup ({  
        cache: false  
    });



</script>
Select a movie to crawl <br />
<form action="crawlerfinal.php">
<select name="movies" id="movies" >';
for($i=0;$i < $cou;$i++){
echo '<option value="'.$rak[$i].'">'.$rak[$i].'</option>';
}
echo '</select>

<input type="submit" value="Submit" />
</form>';
// movie form 

// range of id's
echo'
Select the range of id to crawl
<form action="crawlerfinal.php" >
 FROM: <input type="integer" id="from" name = "from" min = 0 /> <br />
 TO: <input type="integer" id="to" name = "to" min = 0 /> <br />
 <input type="submit" value="Submit" />
</form>';

?>
<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByName('foo[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
<form action="takeaction.php" method="get">


<input type="checkbox" name="foo[]" value="bar1"> Bar 1<br/>
<input type="checkbox" name="foo[]" value="bar2"> Bar 2<br/>
<input type="checkbox" name="foo[]" value="bar3"> Bar 3<br/>
<input type="checkbox" name="foo[]" value="bar4"> Bar 4<br/><input type="checkbox" onClick="toggle(this)" /> Toggle All<br/>
<input type="submit" name="type" value="submit1"/>
<input type="submit" name="type" value="submit2"/>
</form>

