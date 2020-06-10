<?php
/**
 * Altis Documentation Module.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
