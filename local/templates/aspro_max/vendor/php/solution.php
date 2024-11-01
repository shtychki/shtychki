<?

namespace {
	if (!defined('VENDOR_PARTNER_NAME')) {
		/** @const Aspro partner name */
		define('VENDOR_PARTNER_NAME', 'aspro');
	}

	if (!defined('VENDOR_SOLUTION_NAME')) {
		/** @const Aspro solution name */
		define('VENDOR_SOLUTION_NAME', 'max');
	}

	if (!defined('VENDOR_MODULE_ID')) {
		/** @const Aspro module id */
		define('VENDOR_MODULE_ID', 'aspro.max');
	}

	foreach ([
		'CMax' => 'TSolution',
		'CMaxCache' => 'TSolution\Cache',
		'CMaxCondition' => 'TSolution\Condition',
		'CMaxEvents' => 'TSolution\Events',
		'CMaxRegionality' => 'TSolution\Regionality',
		'Aspro\Functions\CAsproMax' => 'TSolution\Functions',
		'Aspro\Max\Functions\Extensions' => 'TSolution\Extensions',
		'Aspro\Max\Social\Factory' => 'TSolution\Social\Factory',
		'Aspro\Max\Social\Video\Factory' => 'TSolution\Social\Video\Factory',
		'Aspro\Max\Utils' => 'Tsolution\Utils',
		'Aspro\Max\Filter' => 'TSolution\Filter',
	] as $original => $alias) {
		if (!class_exists($alias)) {
			class_alias($original, $alias);
		}
	}

	// these alias declarations for IDE only
	if (false) {
		class TSolution extends CMax {}
	}
}

// these alias declarations for IDE only
namespace TSolution {
	if (false) {		
		class Cache extends \CMaxCache {}
		class Condition extends \CMaxCondition {}
		class Events extends \CMaxEvents {}
		class Functions extends \Aspro\Functions\CAsproMax {}
		class Extensions extends \Aspro\Max\Functions\Extensions {}
		class Regionality extends \CMaxRegionality {}
		class Utils extends \Aspro\Max\Utils {}
		class Filter extends \Aspro\Max\Filter {}
	}
}

namespace TSolution\Social {
	if (false) {
		class Factory extends \Aspro\Max\Social\Factory {}
	}
}

namespace TSolution\Social\Video {
	if (false) {
		class Factory extends \Aspro\Max\Social\Video\Factory {}
	}
}