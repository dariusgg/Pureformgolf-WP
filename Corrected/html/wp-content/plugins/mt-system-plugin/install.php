defined( 'MT_SYSTEM_PLUGIN_INSTALL_PLUGIN_PATH' ) or define( 'MT_SYSTEM_PLUGIN_INSTALL_PLUGIN_PATH', 'mt-system-plugin/mt-system-plugin.php' );
function install_mt_system_plugin() {
global $pagenow;
 
if ( !( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
return;
}
$active_plugins = (array) get_option( 'active_plugins', array() );
 
// Shouldn't happen, but avoid duplicate entries just in case.
if ( !empty( $active_plugins ) && false !== array_search( MT_SYSTEM_PLUGIN_INSTALL_PLUGIN_PATH, $active_plugins ) ) {
return;
}
 
$active_plugins[] = MT_SYSTEM_PLUGIN_INSTALL_PLUGIN_PATH;
update_option( 'active_plugins', $active_plugins );
}
add_action( 'shutdown', 'install_mt_system_plugin' );
