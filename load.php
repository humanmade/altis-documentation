<?php

namespace HM\Platform\Documentation;

const DIRECTORY = __DIR__;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/ui/namespace.php';

add_action( 'hm-platform.modules.init', __NAMESPACE__ . '\\register' );
