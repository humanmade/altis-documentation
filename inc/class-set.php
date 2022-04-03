<?php
/**
 * Altis Documentation Set. A set is a collection of Groups.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

/**
 * Altis Documentation Set Object.
 * Typically, a related set of documentation groups.
 *
 * @package altis/documentation
 */
class Set {
	/**
	 * Set id . E.g. 'dev-docs', 'user-documentation', etc.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Set title . E.g. 'Developer documentation', 'User documentation', etc.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Groups which belong to the set.
	 *
	 * @var Group[]
	 */
	protected $groups = [];

	/**
	 * Default group ID.
	 *
	 * @var string
	 */
	protected $default_group_id = '';

	/**
	 * Constructor.
	 *
	 * @param string $id Set id.
	 * @param string $title Set title.
	 */
	public function __construct( string $id = '', string $title = '' ) {
		$this->id = $id;
		$this->title = $title;
	}

	/**
	 * Get the Set title.
	 *
	 * @return string
	 */
	public function get_title() : string {
		return $this->title;
	}

	/**
	 * Get the Set id.
	 *
	 * @return string
	 */
	public function get_id() : string {
		return $this->id;
	}

	/**
	 * Add Group to the documentation set.
	 *
	 * @param string $id Group Id
	 * @param Group $group Group object.
	 */
	public function add_group( string $id, Group $group ) : void {
		$this->groups[ $id ] = $group;
	}

	/**
	 * Get all groups which belong to the set.
	 *
	 * @return Group[]
	 */
	public function get_groups() : array {

		/**
		 * Filter documentation groups for this set.
		 *
		 * This allows modules to register additional documentation groups in this set.
		 *
		 * @param Group[] $docs Map of group ID to Group object.
		 * @param string $set_id The documentation set id.
		 */
		return apply_filters( 'altis.documentation.groups', $this->groups, $this->id );
	}

	/**
	 * Get a single group by ID.
	 *
	 * @param string $id The group id.
	 *
	 * @return Group|null Group if set, null otherwise.
	 */
	public function get_group( string $id ) : ?Group {

		return $this->groups[ $id ] ?? null;
	}

	/**
	 * Get the default group ID.
	 *
	 * @return string.
	 */
	public function get_default_group_id() : string {
		if ( ! empty( $this->default_group_id ) ) {
			return $this->default_group_id;
		}

		if ( count( $this->groups ) ) {

			return array_keys( $this->groups )[0];
		}

		return '';
	}

	/**
	 * Set the default group ID.
	 *
	 */
	public function set_default_group_id( string $group_id ) : void {

		$this->default_group_id = $group_id;
	}

}
