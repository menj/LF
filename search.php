<?php
/**
 * Search results.
 *
 * A block parent theme serves its own search template unless the child provides a PHP one,
 * so this reuses index.php (which already renders the search heading and results) to keep
 * the site's own header and footer.
 *
 * @package Langgam_Fikir
 */

require locate_template( 'index.php' );
