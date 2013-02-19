<?php
/*******
 * this function named [module_name]_delete is called whenever a course content is deleted
 * which includes when restoring a backup with override set, or when deleting an entire course.
 * the function must delete all module-specific material associated with this course.
 * $course is the ID of the course to delete.
 */

function fluid_player_delete($course) {
	global $db;

/*
	// delete fluid_player course table entries
	$sql = "DELETE FROM ".TABLE_PREFIX."fluid_player WHERE course_id=$course";
	mysql_query($sql, $db);
*/

	// delete fluid_player course files
	$path = AT_CONTENT_DIR .'fluid_player/' . $course .'/';
	clr_dir($path);
}

?>