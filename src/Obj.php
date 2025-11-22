<?php
/**
 * Object Utilities
 *
 * Minimal, memorable object manipulation utilities for everyday PHP development.
 * Focused on the most common operations developers actually need.
 *
 * @package ArrayPress\ObjectUtils
 * @since   1.0.0
 * @author  David Sherlock
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\ObjectUtils;

use ReflectionObject;
use stdClass;

/**
 * Object manipulation utilities.
 *
 * Provides simple, memorable methods for common object operations
 * like tracking changes, applying updates, and accessing nested properties.
 */
class Obj {

	/**
	 * Get what changed between an object and new data.
	 *
	 * Compares existing object properties with new data and returns
	 * only the properties that have different values.
	 *
	 * @param object $object The existing object to compare against.
	 * @param array  $data   New data to compare with object properties.
	 *
	 * @return array Array of changed properties with their new values.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * $user = (object) ['name' => 'John', 'email' => 'john@example.com'];
	 * $data = ['name' => 'John', 'email' => 'new@example.com', 'age' => 30];
	 * $changes = Obj::changes($user, $data);
	 * // Returns: ['email' => 'new@example.com']
	 */
	public static function changes( object $object, array $data ): array {

		return array_filter( $data, function ( $value, $key ) use ( $object ) {
			return property_exists( $object, $key ) && $object->$key !== $value;
		}, ARRAY_FILTER_USE_BOTH );
	}

	/**
	 * Apply changes to an object.
	 *
	 * Updates object properties with new values. Only updates properties
	 * that already exist on the object.
	 *
	 * @param object $object  The object to update.
	 * @param array  $changes Array of property => value pairs to apply.
	 *
	 * @return int Number of properties that were updated.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * $user = (object) ['name' => 'John', 'email' => 'john@example.com'];
	 * $count = Obj::apply($user, ['name' => 'Jane', 'age' => 30]);
	 * // Updates name, ignores age (doesn't exist), returns 1
	 */
	public static function apply( object $object, array $changes ): int {
		$count = 0;

		foreach ( $changes as $key => $value ) {
			if ( property_exists( $object, $key ) ) {
				$object->$key = $value;
				$count ++;
			}
		}

		return $count;
	}

	/**
	 * Get value from nested object/array using dot notation.
	 *
	 * Safely traverses nested objects and arrays to retrieve a value
	 * at the specified path.
	 *
	 * @param object     $object  The object to traverse.
	 * @param string     $path    Dot-notated path (e.g., 'user.address.city').
	 * @param mixed|null $default Default value if path doesn't exist.
	 *
	 * @return mixed The value at the path or default if not found.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * $data = (object) ['user' => (object) ['address' => ['city' => 'NYC']]];
	 * $city = Obj::get($data, 'user.address.city');
	 * // Returns: 'NYC'
	 * $country = Obj::get($data, 'user.address.country', 'USA');
	 * // Returns: 'USA' (default)
	 */
	public static function get( object $object, string $path, $default = null ) {
		$keys    = explode( '.', $path );
		$current = $object;

		foreach ( $keys as $key ) {
			if ( is_object( $current ) && property_exists( $current, $key ) ) {
				$current = $current->$key;
			} elseif ( is_array( $current ) && isset( $current[ $key ] ) ) {
				$current = $current[ $key ];
			} else {
				return $default;
			}
		}

		return $current;
	}

	/**
	 * Pick specific properties from an object.
	 *
	 * Creates a new object containing only the specified properties
	 * from the source object.
	 *
	 * @param object $object The source object.
	 * @param array  $keys   Array of property names to extract.
	 *
	 * @return stdClass New object with only the specified properties.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * $user = (object) ['id' => 1, 'name' => 'John', 'password' => 'secret'];
	 * $public = Obj::pick($user, ['id', 'name']);
	 * // Returns: (object) ['id' => 1, 'name' => 'John']
	 */
	public static function pick( object $object, array $keys ): stdClass {
		$result = new stdClass();

		foreach ( $keys as $key ) {
			if ( property_exists( $object, $key ) ) {
				$result->$key = $object->$key;
			}
		}

		return $result;
	}

	/**
	 * Convert object to array.
	 *
	 * Converts an object to an associative array. Can optionally include
	 * private and protected properties.
	 *
	 * @param object $object The object to convert.
	 * @param bool   $all    Whether to include private/protected properties.
	 *
	 * @return array The object as an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @example
	 * $user = (object) ['name' => 'John', 'email' => 'john@example.com'];
	 * $array = Obj::to_array($user);
	 * // Returns: ['name' => 'John', 'email' => 'john@example.com']
	 */
	public static function to_array( object $object, bool $all = false ): array {
		if ( ! $all ) {
			return get_object_vars( $object );
		}

		$reflection = new ReflectionObject( $object );
		$properties = $reflection->getProperties();
		$result     = [];

		foreach ( $properties as $property ) {
			$property->setAccessible( true );
			$result[ $property->getName() ] = $property->getValue( $object );
		}

		return $result;
	}

}