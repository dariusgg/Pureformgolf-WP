<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Display growl messages to users
 *
 * @version 1.1.0
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 */
final class GD_System_Plugin_Messages {

	/**
	 * Option key for messages
	 *
	 * @var string
	 */
	const OPTION_KEY = 'gd_system_messages';

	/**
	 * User cap required to see messages
	 *
	 * @var string
	 */
	const CAP = 'activate_plugins';

	/**
	 * Array of messages
	 *
	 * @var array
	 */
	private $messages = array();

	/**
	 * Construct
	 */
	public function __construct() {

		$this->messages = (array) get_option( static::OPTION_KEY, array() );

		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * Display any system messages to the user
	 *
	 * @action init
	 */
	public function init() {

		if ( ! current_user_can( static::CAP ) || empty( $this->messages ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		// @codeCoverageIgnoreStart

		wp_enqueue_script( 'gd_system_gritter', GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/js/jquery.gritter{$suffix}.js", array( 'jquery' ), '1.7.4' );

		wp_enqueue_style( 'gd_system_gritter', GD_SYSTEM_PLUGIN_URL . "gd-system-plugin/css/jquery.gritter{$suffix}.css", array(), '0.0.1' );

		// @codeCoverageIgnoreEnd

		add_action( 'admin_bar_menu', array( $this, 'display_messages' ) );

	}

	/**
	 * Display any system messages to the user
	 *
	 * @action admin_bar_menu
	 */
	public function display_messages() {

		foreach ( $this->messages as $message ) {

			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					$.gritter.add( {
						title: "<?php echo esc_js( __( 'Success', 'gd_system' ) ) ?>",
						text: "<?php echo esc_js( $message ) ?>",
						time: <?php echo absint( 5 * 1000 ) ?>
					} );
				} );
			</script>
			<?php

		}

		// If there are no more messages, delete. Otherwise, save.
		delete_option( static::OPTION_KEY );

	}

	/**
	 * Add a message to be displayed to the user
	 *
	 * @param string $message
	 */
	public function add_message( $message ) {

		$this->messages[] = $message;

		update_option( static::OPTION_KEY, $this->messages );

	}

}
