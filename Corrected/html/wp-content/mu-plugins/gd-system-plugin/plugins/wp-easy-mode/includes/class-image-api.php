<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Class Image_API
 *
 * Handle fetching of image based on category
 */
final class Image_API {

	/**
	 * Constant used to interact with the API
	 */
	const BASE_URL      = 'https://d3.godaddy.com/api/v1/';
	const IMAGE_ENPOINT = 'stock_photos/';
	const CAT_ENPOINT   = 'categories/';
	const TOKEN         = '53dacdceba099a43ed4fb45b491b16c4afb37d48';

	/**
	 * Hold transient base namespace
	 *
	 * @const string
	 */
	const TRANSIENT_BASE = 'wpem_image_api_';


	/**
	 * Var to hold full url
	 */
	private $image_cat_url;

	/**
	 * Image_API constructor.
	 */
	public function __construct() {

		$this->image_cat_url = static::BASE_URL . static::IMAGE_ENPOINT . 'category/%s/';

	}

	/**
	 * Retrieve json response from one category and store it as a transient for later use
	 *
	 * @param string $wpem_cat
	 * @return object array of objects
	 */
	public function get_images_by_cat( $wpem_cat ) {

		if ( false === ( $category = $this->get_api_cat( $wpem_cat ) ) ) {

			return [];

		}

		// Check if we have a transient cached response for that call
		if ( $data = get_transient( static::TRANSIENT_BASE . $category ) ) {

			return $data;

		}

		$data = $this->fetch( sprintf( $this->image_cat_url, $category ) );

		if ( $data ) {

			set_transient( static::TRANSIENT_BASE . $category, $data, HOUR_IN_SECONDS );

		}

		return $data;

	}

	/**
	 * Get api category from wpem category
	 *
	 * @param string $wpem_cat
	 *
	 * @return bool|string
	 */
	private function get_api_cat( $wpem_cat ) {

		$list = wpem_get_site_industry_slugs_to( 'api_cat' );

		if ( isset( $list[ $wpem_cat ] ) ) {

			return $list[ $wpem_cat ];

		}

		return false;

	}

	/**
	 * Helper to fetch infomation from the api
	 *
	 * @param string $url
	 * @return array|bool|mixed|object
	 */
	private function fetch( $url ) {

		$response = wp_remote_get(
			$url,
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Authorization' => 'Token ' . static::TOKEN,
				],
			]
		);

		if ( is_wp_error( $response ) ) {

			return false;

		}

		$json = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $json->count, $json->results ) && $json->count >= 1 ) {

			return $json->results;

		}

		return false;

	}

}
