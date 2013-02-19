<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_FLUID_PLAYER',       $this->getPrivilege());
define('AT_ADMIN_PRIV_FLUID_PLAYER', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['fluid_player'] = array('title_var'=>'fluid_player', 'file'=>'mods/fluid_player/side_menu.inc.php');
// ** possible alternative: **
// $this->addStack('fluid_player', array('title_var' => 'fluid_player', 'file' => './side_menu.inc.php');

/*******
 * create optional sublinks for module "detail view" on course home page
 * when this line is uncommented, "mods/fluid_player/sublinks.php" need to be created to return an array of content to be displayed
 */
//$this->_list['fluid_player'] = array('title_var'=>'fluid_player','file'=>'mods/fluid_player/sublinks.php');

// Uncomment for tiny list bullet icon for module sublinks "icon view" on course home page
//$this->_pages['mods/fluid_player/index.php']['icon']      = 'mods/fluid_player/fluid_player_sm.jpg';

// Uncomment for big icon for module sublinks "detail view" on course home page
//$this->_pages['mods/fluid_player/index.php']['img']      = 'mods/fluid_player/fluid_player.jpg';

// ** possible alternative: **
// the text to display on module "detail view" when sublinks are not available
$this->_pages['mods/fluid_player/index.php']['text']      = _AT('fluid_player_text');

/*******
 * if this module is to be made available to students on the Home or Main Navigation.
 */
$_group_tool = $_student_tool = 'mods/fluid_player/index.php';

/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_FLUID_PLAYER, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/fluid_player/index_admin.php');
	$this->_pages['mods/fluid_player/index_admin.php']['title_var'] = 'fluid_player';
	$this->_pages['mods/fluid_player/index_admin.php']['parent']    = AT_NAV_ADMIN;
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/fluid_player/index_instructor.php']['title_var'] = 'fluid_player';
$this->_pages['mods/fluid_player/index_instructor.php']['parent']   = 'tools/index.php';
// ** possible alternative: **
// $this->pages['./index_instructor.php']['title_var'] = 'fluid_player';
// $this->pages['./index_instructor.php']['parent']    = 'tools/index.php';

/*******
 * student page.
 */
$this->_pages['mods/fluid_player/index.php']['title_var'] = 'fluid_player';
$this->_pages['mods/fluid_player/index.php']['img']       = 'mods/fluid_player/fluid_player.jpg';

/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/fluid_player/index_public.php');
$this->_pages['mods/fluid_player/index_public.php']['title_var'] = 'fluid_player';
$this->_pages['mods/fluid_player/index_public.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/fluid_player/index_mystart.php');
$this->_pages['mods/fluid_player/index_mystart.php']['title_var'] = 'fluid_player';
$this->_pages['mods/fluid_player/index_mystart.php']['parent'] = AT_NAV_START;

function fluid_player_get_group_url($group_id) {
	return 'mods/fluid_player/index.php';
}
?>