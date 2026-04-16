<?php

namespace WPGraphQLGutenberg\Admin;

use WPGraphQLGutenberg\Admin\Editor;
use WPGraphQLGutenberg\Blocks\BlocksJSON;
use WPGraphQLGutenberg\PostTypes\BlockEditorPreview;

class Settings {
	public function __construct() {
		add_action('admin_init', function () {
			register_setting(
				'wp_graphql_gutenberg',
				WP_GRAPHQL_GUTENBERG_BLOCKS_JSON_FILTERED_PROPERTIES_OPTION_NAME,
				[
					'sanitize_callback' => [ BlocksJSON::class, 'sanitize_filtered_properties' ],
					'type'              => 'array',
					'default'           => [],
				]
			);
		});

		add_action('admin_menu', function () {
			add_menu_page(
				__( 'GraphQL Gutenberg', 'wp-graphql-gutenberg' ),
				'GraphQL Gutenberg',
				'manage_options',
				'wp-graphql-gutenberg-admin',
				function () {
					$filtered_properties = BlocksJSON::get_filtered_properties();
					echo '<div class="wrap">';
					echo '<div id="wp-graphql-gutenberg-admin"></div>';
					echo '<h2>' . esc_html__( 'blocksJSON Filter', 'wp-graphql-gutenberg' ) . '</h2>';
					echo '<form method="post" action="options.php">';
					settings_fields( 'wp_graphql_gutenberg' );
					echo '<table class="form-table" role="presentation"><tbody><tr>';
					echo '<th scope="row"><label for="wp-graphql-gutenberg-blocks-json-filtered-properties">' . esc_html__( 'Filtered property names', 'wp-graphql-gutenberg' ) . '</label></th>';
					echo '<td>';
					echo '<textarea class="large-text code" rows="6" id="wp-graphql-gutenberg-blocks-json-filtered-properties" name="' . esc_attr( WP_GRAPHQL_GUTENBERG_BLOCKS_JSON_FILTERED_PROPERTIES_OPTION_NAME ) . '">' . esc_textarea( implode( "\n", $filtered_properties ) ) . '</textarea>';
					echo '<p class="description">' . esc_html__( 'One property name per line (or comma separated). These properties will be removed from blocksJSON responses.', 'wp-graphql-gutenberg' ) . '</p>';
					echo '</td></tr></tbody></table>';
					submit_button( __( 'Save blocksJSON filter', 'wp-graphql-gutenberg' ) );
					echo '</form>';
					echo '</div>';
				},
				'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0MDAgNDAwIj48cGF0aCBmaWxsPSIjRTEwMDk4IiBkPSJNNTcuNDY4IDMwMi42NmwtMTQuMzc2LTguMyAxNjAuMTUtMjc3LjM4IDE0LjM3NiA4LjN6Ii8+PHBhdGggZmlsbD0iI0UxMDA5OCIgZD0iTTM5LjggMjcyLjJoMzIwLjN2MTYuNkgzOS44eiIvPjxwYXRoIGZpbGw9IiNFMTAwOTgiIGQ9Ik0yMDYuMzQ4IDM3NC4wMjZsLTE2MC4yMS05Mi41IDguMy0xNC4zNzYgMTYwLjIxIDkyLjV6TTM0NS41MjIgMTMyLjk0N2wtMTYwLjIxLTkyLjUgOC4zLTE0LjM3NiAxNjAuMjEgOTIuNXoiLz48cGF0aCBmaWxsPSIjRTEwMDk4IiBkPSJNNTQuNDgyIDEzMi44ODNsLTguMy0xNC4zNzUgMTYwLjIxLTkyLjUgOC4zIDE0LjM3NnoiLz48cGF0aCBmaWxsPSIjRTEwMDk4IiBkPSJNMzQyLjU2OCAzMDIuNjYzbC0xNjAuMTUtMjc3LjM4IDE0LjM3Ni04LjMgMTYwLjE1IDI3Ny4zOHpNNTIuNSAxMDcuNWgxNi42djE4NUg1Mi41ek0zMzAuOSAxMDcuNWgxNi42djE4NWgtMTYuNnoiLz48cGF0aCBmaWxsPSIjRTEwMDk4IiBkPSJNMjAzLjUyMiAzNjdsLTcuMjUtMTIuNTU4IDEzOS4zNC04MC40NSA3LjI1IDEyLjU1N3oiLz48cGF0aCBmaWxsPSIjRTEwMDk4IiBkPSJNMzY5LjUgMjk3LjljLTkuNiAxNi43LTMxIDIyLjQtNDcuNyAxMi44LTE2LjctOS42LTIyLjQtMzEtMTIuOC00Ny43IDkuNi0xNi43IDMxLTIyLjQgNDcuNy0xMi44IDE2LjggOS43IDIyLjUgMzEgMTIuOCA0Ny43TTkwLjkgMTM3Yy05LjYgMTYuNy0zMSAyMi40LTQ3LjcgMTIuOC0xNi43LTkuNi0yMi40LTMxLTEyLjgtNDcuNyA5LjYtMTYuNyAzMS0yMi40IDQ3LjctMTIuOCAxNi43IDkuNyAyMi40IDMxIDEyLjggNDcuN00zMC41IDI5Ny45Yy05LjYtMTYuNy0zLjktMzggMTIuOC00Ny43IDE2LjctOS42IDM4LTMuOSA0Ny43IDEyLjggOS42IDE2LjcgMy45IDM4LTEyLjggNDcuNy0xNi44IDkuNi0zOC4xIDMuOS00Ny43LTEyLjhNMzA5LjEgMTM3Yy05LjYtMTYuNy0zLjktMzggMTIuOC00Ny43IDE2LjctOS42IDM4LTMuOSA0Ny43IDEyLjggOS42IDE2LjcgMy45IDM4LTEyLjggNDcuNy0xNi43IDkuNi0zOC4xIDMuOS00Ny43LTEyLjhNMjAwIDM5NS44Yy0xOS4zIDAtMzQuOS0xNS42LTM0LjktMzQuOSAwLTE5LjMgMTUuNi0zNC45IDM0LjktMzQuOSAxOS4zIDAgMzQuOSAxNS42IDM0LjkgMzQuOSAwIDE5LjItMTUuNiAzNC45LTM0LjkgMzQuOU0yMDAgNzRjLTE5LjMgMC0zNC45LTE1LjYtMzQuOS0zNC45IDAtMTkuMyAxNS42LTM0LjkgMzQuOS0zNC45IDE5LjMgMCAzNC45IDE1LjYgMzQuOSAzNC45IDAgMTkuMy0xNS42IDM0LjktMzQuOSAzNC45Ii8+PC9zdmc+'
			);
		});

		add_action('admin_enqueue_scripts', function ( $hook ) {
			if ( ! preg_match( '/.+wp-graphql-gutenberg-admin$/', $hook ) ) {
				return;
			}

			wp_enqueue_style( 'wp-components' );

			Editor::enqueue_script();

			wp_localize_script(Editor::$script_name, 'wpGraphqlGutenberg', [
				'adminPostType' => BlockEditorPreview::post_type(),
				'adminUrl'      => get_admin_url(),
			]);
		});
	}
}
