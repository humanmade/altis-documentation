<?php

namespace Altis\Documentation; // @codingStandardsIgnoreLine

const DIRECTORY = __DIR__;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/ui/namespace.php';

// Don't self-initialize if this is not an Altis execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
