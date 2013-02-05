<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$_pages['mods/_standard/links/index.php']['children']  = array('mods/_standard/links/add.php');
}

if (isset($_GET['view'])) {
	$_GET['view'] = intval($_GET['view']);
	//add to the num hits
    $row = queryDB('SELECT Url, hits FROM %slinks WHERE link_id=%d', array(TABLE_PREFIX, $_GET['view']), true);

    if (count($row) > 0) {
		if (!authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN)) {
			$row['hits']++;
			queryDB('UPDATE %slinks SET hits=%s WHERE link_id=%d', array(TABLE_PREFIX, $row['hits'], $_GET['view']));
		}
		
		//http://www.atutor.ca/atutor/mantis/view.php?id=3853
		$is_http = preg_match("/^http/", $row['Url']);
		if ($is_http==0){
			$row['Url'] = 'http://' . $row['Url'];
		}

		//redirect
		header('Location: ' . $row['Url']);
		exit;
    }
}

require (AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['reset_filter']) {
	unset($_GET);
}

$_GET['cat_parent_id'] = intval($_GET['cat_parent_id']);

//get appropriate categories
$categories = get_link_categories();

//ascending decscending columns...
$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('LinkName' => 1, 'name' => 1, 'description' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'LinkName';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'LinkName';
} else {
	// no order set
	$order = 'asc';
	$col   = 'LinkName';
}

//search
if ($_GET['search']) {
	$_GET['search'] = trim($_GET['search']);
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = str_replace(array('%','_'), array('\%', '\_'), $search);
	$search = '%'.$search.'%';
	$search = "((LinkName LIKE '$search') OR (description LIKE '$search'))";
} else {
	$search = '1';
}

//view links of a child category
if ($_GET['cat_parent_id']) {
    $children = get_child_categories ($_GET['cat_parent_id'], $categories);
    $cat_sql = "C.cat_id IN ($children $_GET[cat_parent_id])";
	$parent_id = intval($_GET['cat_parent_id']);
} else {
    $cat_sql = '1';   
    $parent_id = 0;	
}

//get links
$tmp_groups = implode(',', $_SESSION['groups']);

if (!empty($tmp_groups)) {
	$result = queryDB('SELECT * FROM %slinks L INNER JOIN %slinks_categories C USING (cat_id) WHERE ((owner_id=%d AND owner_type=%s) OR (owner_id IN (%s) AND owner_type=%s)) AND L.Approved=1 AND %s AND %s ORDER BY %s %s', array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], LINK_CAT_COURSE, $tmp_groups, LINK_CAT_GROUP, $search, $cat_sql, $col, $order));
} else {
	$result = queryDB('SELECT * FROM %slinks L INNER JOIN %slinks_categories C USING (cat_id) WHERE (owner_id=%d AND owner_type=%s) AND L.Approved=1 AND %s AND %s ORDER BY %s %s', array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], LINK_CAT_COURSE, $search, $cat_sql, $col, $order));
}
$num_results = count($result);

?>
<?php if ($num_results > 0 || isset($_GET['filter'])): ?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
        <div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

        <div class="row">
			<label for="category_parent"><?php echo _AT('select_cat'); ?></label>
	        <br />
			<?php if (!empty($categories)): ?>
				<select name="cat_parent_id" id="category_parent"><?php
						if ($parent_id) {
							$current_cat_id = $parent_id;
							$exclude = false; /* don't exclude the children */
						} else {
							$current_cat_id = $cat_id;
							$exclude = true; /* exclude the children */
						}

						echo '<option value="0">&nbsp;&nbsp;&nbsp; '._AT('cats_all').' &nbsp;&nbsp;&nbsp;</option>';
						select_link_categories($categories, 0, $current_cat_id, FALSE);
					?>
				</select>
			<?php endif; ?>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>


		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
</div>
</form>
<?php endif; ?>

<table class="data static" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'LinkName'): ?>
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'name'): ?>
		<col />
		<col class="sort" />
		<col />
	<?php elseif($col == 'description'): ?>
		<col span="2" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col"><a href="<?php echo url_rewrite('mods/_standard/links/index.php?'.$orders[$order].'=LinkName'.$page_string); ?>"><?php echo _AT('title');          ?></a></th>
	<th scope="col"><a href="<?php echo url_rewrite('mods/_standard/links/index.php?'.$orders[$order].'=name'.$page_string); ?>"><?php echo _AT('category');           ?></a></th>
	<th scope="col"><a href="<?php echo url_rewrite('mods/_standard/links/index.php?'.$orders[$order].'=description'.$page_string); ?>"><?php echo _AT('description'); ?></a></th>
</tr>
</thead>
<tbody>
	<?php
	if (!empty($result)) {
	   foreach($result as $i=>$value) {
	   $row = $result[$i];
		?>
		<tr onmousedown="document.form['m<?php echo $row['link_id']; ?>'].checked = true;">
			<td><a href="<?php echo url_rewrite('mods/_standard/links/index.php?view='.$row['link_id']); ?>" target="_new" title="<?php echo AT_print($row['LinkName'], 'links.LinkName'); ?>"><?php echo AT_print($row['LinkName'], 'links.LinkName'); ?></a></td>
			<td><?php 
				if (empty($row['name'])) {
					$row['name'] = get_group_name($row['owner_id']);
				}
				echo AT_print($row['name'], 'links.name'); 
			?></td>
			<td><?php echo AT_print($row['Description'], 'links.Description'); ?></td>
		</tr>
		<?php 
        }
    } else {
		?>
		<tr><td colspan="3"><?php echo _AT('none_found'); ?></td></tr>
<?php } ?>
</tbody>
</table>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
