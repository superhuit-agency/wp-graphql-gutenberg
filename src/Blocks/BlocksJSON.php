<?php

namespace WPGraphQLGutenberg\Blocks;

class BlocksJSON {
	public static function sanitize_filtered_properties( $value ) {
		$properties = [];

		if ( is_string( $value ) ) {
			$properties = preg_split( '/[\r\n,]+/', $value );
		} elseif ( is_array( $value ) ) {
			$properties = $value;
		}

		return array_values(
			array_unique(
				array_filter(
					array_map(fn( $property ) => trim( sanitize_text_field( $property ) ), $properties),
					function ( $property ) {
						return '' !== $property;
					}
				)
			)
		);
	}

	public static function filter_properties( $value, $properties ) {
		if ( empty( $properties ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				if ( is_string( $key ) && in_array( $key, $properties, true ) ) {
					unset( $value[ $key ] );
					continue;
				}

				$value[ $key ] = self::filter_properties( $item, $properties );
			}

			return $value;
		}

		if ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $item ) {
				if ( in_array( $key, $properties, true ) ) {
					unset( $value->$key );
					continue;
				}

				$value->$key = self::filter_properties( $item, $properties );
			}
		}

		return $value;
	}

	public static function render_dynamic_contents( $blocks ) {
		foreach ( $blocks as $block ) {
			// Inner blocks first, keeping the render order (and generated ids) of eager rendering.
			self::render_dynamic_contents( $block->innerBlocks );
			$block->get_dynamic_content();
		}
	}

	public static function encode_blocks( $blocks, $model ) {
		$properties = self::sanitize_filtered_properties(
			apply_filters( 'graphql_gutenberg_blocks_json_filtered_properties', [], $model, $blocks )
		);

		if ( ! in_array( 'dynamicContent', $properties, true ) ) {
			self::render_dynamic_contents( $blocks );
		}

		$filtered_blocks = self::filter_properties( $blocks, $properties );

		return wp_json_encode(
			apply_filters( 'graphql_gutenberg_blocks_json', $filtered_blocks, $model, $blocks )
		);
	}
}
