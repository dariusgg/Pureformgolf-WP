<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Step_Settings extends Step {

	/**
	 * Fields object
	 *
	 * @var object
	 */
	private $fields;

	/**
	 * Hold the image api we got by dependency injection
	 *
	 * @var object
	 */
	private $image_api;

	/**
	 * Class constructor
	 *
	 * @param Log       $log
	 * @param Image_API $image_api
	 */
	public function __construct( Log $log, Image_API $image_api ) {

		$this->image_api = $image_api;

		parent::__construct( $log );

		$this->args = [
			'name'       => 'settings',
			'title'      => __( 'Settings', 'wp-easy-mode' ),
			'page_title' => __( 'Settings', 'wp-easy-mode' ),
			'can_skip'   => false,
		];

	}

	/**
	 * Step init
	 */
	protected function init() {

		$fields = [
			[
				'name'        => 'wpem_site_type',
				'label'       => __( 'Type', 'wp-easy-mode' ),
				'type'        => 'radio',
				'sanitizer'   => 'sanitize_key',
				'description' => __( 'What type of website would you like to create?', 'wp-easy-mode' ),
				'value'       => wpem_get_site_type(),
				'required'    => true,
				'choices'     => [
					'standard' => __( 'Website + Blog', 'wp-easy-mode' ),
					'blog'     => __( 'Blog only', 'wp-easy-mode' ),
					'store'    => __( 'Online Store', 'wp-easy-mode' ),
				],
			],
			[
				'name'        => 'wpem_site_industry',
				'label'       => __( 'Industry', 'wp-easy-mode' ),
				'type'        => 'select',
				'sanitizer'   => 'sanitize_key',
				'description' => __( 'What will your website be about?', 'wp-easy-mode' ),
				'value'       => wpem_get_site_industry(),
				'required'    => true,
				'choices'     => wpem_get_site_industry_slugs_to( 'label' ),
			],
			[
				'name'        => 'blogname',
				'label'       => __( 'Title', 'wp-easy-mode' ),
				'type'        => 'text',
				'sanitizer'   => function( $value ) {
					return stripcslashes( sanitize_option( 'blogname', $value ) );
				},
				'description' => __( 'The title of your website appears at the top of all pages and in search results.', 'wp-easy-mode' ),
				'value'       => get_option( 'blogname' ),
				'required'    => true,
				'atts'        => [
					'placeholder' => __( 'Enter your website title here', 'wp-easy-mode' ),
				],
			],
			[
				'name'        => 'blogdescription',
				'label'       => __( 'Tagline', 'wp-easy-mode' ),
				'type'        => 'text',
				'sanitizer'   => function( $value ) {
					return stripcslashes( sanitize_option( 'blogdescription', $value ) );
				},
				'description' => __( 'Think of the tagline as a slogan that describes what makes your website special. It will also appear in search results.', 'wp-easy-mode' ),
				'value'       => get_option( 'blogdescription' ),
				'required'    => true,
				'atts'        => [
					'placeholder' => __( 'Enter your website tagline here', 'wp-easy-mode' ),
				],
			],
		];

		$this->fields = new Fields( $fields );

		add_action( 'wpem_template_notices', [ $this->fields, 'error_notice' ] );

	}

	/**
	 * Step content
	 */
	public function content() {

		printf(
			'<p class="lead-text align-center">%s</p>',
			__( 'Please tell us more about your website (all fields are required)', 'wp-easy-mode' )
		);

		$this->fields->display();

		/**
		 * Fires after the Settings content
		 */
		do_action( 'wpem_step_settings_after_content' );

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Continue', 'wp-easy-mode' ) ?>">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$saved = $this->fields->save();

		// No need to fetch api again if fields  updated

		// Once all the fields are saved, let's query our image api with the saved category
		$site_industry = isset( $saved['wpem_site_industry'] ) ? $saved['wpem_site_industry'] : false;
		$site_type     = isset( $saved['wpem_site_type'] ) ? $saved['wpem_site_type'] : false;

		if ( $site_industry && 'store' !== $site_type ) {

			$this->image_api->get_images_by_cat( $site_industry );

		}

	}

}
