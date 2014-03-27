<?php
namespace Livefyre;

use Livefyre\Core\Network;
/**
 * Starting point to retrieve Network & Site objects. Each object contains methods that assist with 
 * implementation in relation to its respective object.
 */
class Livefyre { 
	/**
	 * Returns a new instance of a Livefyre Network object.
	 *
	 * @param string $networkName Livefyre-provided Network name, e.g., "labs.fyre.co".
	 * @param string $networkKey The Livefyre-provided key/id for the website or application the collection belongs
	 *            to, e.g., "303617"
	 * @return a Network object
	 */
	public static function getNetwork($networkName, $networkKey) { 
		return new Network($networkName, $networkKey);
	} 
}
