<?php
/**
 * Loglevel class file
 *
 * @copyright 2015-2016 iThoughts Informatique
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @package iThoughts\iThoughts WordPress Plugin Toolbox
 * @author Gerkin
 *
 * @version 6.0
 */

namespace ithoughts\v6_0;

if ( ! defined( 'ABSPATH' ) ) {
	 status_header( 403 );wp_die("Forbidden");// Exit if accessed directly.
}

if ( ! class_exists( __NAMESPACE__ . '\\LogLevel' ) ) {
	class LogLevel {

		const SILENT = 0;
		const ERROR = 1;
		const WARN = 2;
		const INFO = 3;
		const SILLY = 4;
	}
}
