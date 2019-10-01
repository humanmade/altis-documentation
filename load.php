<?php

namespace Altis\Documentation; // @codingStandardsIgnoreLine

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
