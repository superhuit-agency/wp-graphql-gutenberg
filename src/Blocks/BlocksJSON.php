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

	public static function encode_blocks( $blocks, $model ) {
		$properties      = apply_filters( 'graphql_gutenberg_blocks_json_filtered_properties', [], $model, $blocks );
		$filtered_blocks = self::filter_properties( $blocks, self::sanitize_filtered_properties( $properties ) );

		return wp_json_encode(
			apply_filters( 'graphql_gutenberg_blocks_json', $filtered_blocks, $model, $blocks )
		);
	}
}
