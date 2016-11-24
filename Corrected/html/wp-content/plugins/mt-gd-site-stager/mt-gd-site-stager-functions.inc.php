<?php

function response_ok($response) {
    $b_kosher = 0;
    if ( !empty( $response ) & is_array( $response ) ) {
        if ( isset( $response['httpcode'] ) ) {
            if ( $response['httpcode'] == 200 ) {
                if ( !empty( $response['data'] ) ) {
                    $b_kosher = 1;
                }
            }
        }
    }
    return $b_kosher;
}

function response_http_error($response) {
    $is_error = 0;
    if ( !empty( $response ) & is_array( $response ) ) {
        if ( isset( $response['httpcode'] ) ) {
            if ( $response['httpcode'] != 200 ) {
                $is_error = 1;
            }
        }
    }
    return $is_error;
}

function response_curl_error($response) {
    $is_error = 0;
    if ( !empty( $response ) & is_array( $response ) ) {
        if ( isset( $response['curlerr'] ) ) {
            $is_error = 1;
        }
    }
    return $is_error;
}

/**
 * Sanitize message
 * ugly hack to remove code references in messages
 *
 * @param $msg string message to sanitize
 *
 * @return string sanitized message
 */
function sanitize_msg( $msg ) {
    $pattern = '/ at \/usr\/share\/.+/';
    if ( preg_match( $pattern, $msg, $matches ) ) {
        if ( !empty( $matches ) && is_array( $matches ) ) {
            $msg = substr( $msg, 0, -strlen( $matches[0] ) );
        }
    }
    return $msg;
}

/**
 * Makes HTTP Request - assumes a json encoded response
 * e.g. $response = http_call('POST','/rest/wpaas/create_staging_account','',array( 'domain_name' => $domain_name, 'db_name' => $db_name ));
 *
 * @param $method  GET/PUT/POST/etc 
 * @param $url     url e.g. http://your.domain.com:1234,
 * @param $uri     uri 
 * @param $query   query to perform 
 * @param $data    array of post fields
 * @param $options array of options   
 *
 * @return array {      
 *   'curlerr'  => curl error
 *   'httpcode' => httpcode,     
 *   'data'     => result from the http call      
 * }
 */
function http_call ( $method, $url, $uri, $query=NULL, $data=NULL, $options=NULL ) {
    $c = curl_init();
    $url = $url.$uri."?".$query;
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);

    //SSL Settings
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);

    //Timeout Setting
    $timeout = 10;
    if ( !empty( $options ) ) {
        if ( isset( $options['timeout'] ) ) {
            $timeout = $options['timeout'];
        }
    }
    curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($c, CURLOPT_DNS_CACHE_TIMEOUT, 300); // 5 minutes

    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($c, CURLOPT_FORBID_REUSE, 1);  // force connection to close and not be pooled for reuse
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);  // don't use a cached version of the url

    $output = curl_exec($c);
    if ( !curl_errno($c) ) {
        $httpcode = curl_getinfo($c, CURLINFO_HTTP_CODE);
    } else {
        $curlerr = curl_error($c);
    }
    curl_close($c);
    return array( 'curlerr' => $curlerr, 'httpcode' => $httpcode, 'data' => json_decode($output));
}

/**
 * Makes XMLRPC call
 *
 * @param $rpc_endpoint XMLRPC Server Endpoint
 *                      (e.g. http://your.domain.com:1234,
 *                      http://your.domain.com/xmlrpc, etc..)
 * @param $rpc_method RPC Method Name to invoke on server side
 * @param $rpc_params Parameters to pass to the RPC Method Name
 *
 * @return mixed      Either an array, integer, string, or boolean
 *                    according to the response returned by the XMLRPC method
 */
function xmlrpc_call( $rpc_endpoint, $rpc_method, $rpc_params ) {
    $request = xmlrpc_encode_request( $rpc_method, $rpc_params );
/* For authorization, consider
    $auth = base64_encode($username.':'.$password); 
    $header = "Content-Type: text/xml\r\nAuthorization: Basic $auth" // may need array('Content-Type: text/xml',"Authorization: Basic $auth")
*/
    $header = 'Content-Type: text/xml'; // may need array('Content-Type: text/xml')
    $context = stream_context_create( array('http' => array(
        'method' => 'POST',
        'header' => $header,
        'content' => $request
    )));
    $response = array();
    if ( function_exists( 'fopen' ) && function_exists( 'ini_get' ) && true == ini_get( 'allow_url_fopen' ) ) {
        $file = file_get_contents( $rpc_endpoint, false, $context );
        $response = xmlrpc_decode( $file );
    } else {
        echo 'Error: allow_url_fopen is off. Contact MT support.<br>';
    }
    return $response;
}

/**
 * Calls MT API to create a stage account. We will use this to create
 * a WordPress site that is a stage of the real WordPress site.
 *
 * @param $domain_name string the site's domain name used for authentication
 * @param $db_name     string the site's database name used for authentication
 * @param $options     array  optional array of options
 *
 * @return array
 */
function mt_api_stage_site( $domain_name, $db_name, $options=NULL ) {
    global $https_server;
    return http_call( 'POST', $https_server, '/rest/wpaas/create_staging_account', '', array( 'domain_name' => $domain_name, 'db_name' => $db_name ), $options);
}

/**
 * Calls MT API to delete a site.
 *
 * @param $domain_name string the site's domain name used for authentication
 * @param $db_name     string the site's database name used for authentication
 * @param $options     array  optional array of options
 *
 * @return bool Success/Fail
 */
function mt_api_delete_site( $domain_name, $db_name, $options=NULL ) {
    global $https_server;
    return http_call( 'POST', $https_server, '/rest/wpaas/delete_staging_account', '', array( 'domain_name' => $domain_name, 'db_name' => $db_name ), $options);
}

/**
 * Calls MT API to delete all staging sites.
 *
 * @param $domain_name string the site's domain name used for authentication
 * @param $db_name     string the site's database name used for authentication
 * @param $options     array  optional array of options
 *
 * @return bool Success/Fail
 */
function mt_api_delete_all_staging_sites( $domain_name, $db_name, $options ) {
    global $https_server;
    return http_call( 'POST', $https_server, '/rest/wpaas/delete_all_staging_accounts','',array( 'domain_name' => $domain_name, 'db_name' => $db_name ), $options );
}

/**
 * Calls MT API to get a list of sites.
 *
 * @param $domain_name string the site's domain name used for authentication
 * @param $db_name     string the site's database name used for authentication
 * @param $options     array  optional array of options
 *
 * @return bool Success/Fail
 */
function mt_api_list_related_accounts( $domain_name, $db_name, $options ) {
    global $https_server;
    $response = http_call( 'POST', $https_server, '/rest/wpaas/list_related_accounts','',array( 'domain_name' => $domain_name, 'db_name' => $db_name ), $options );
    return $response;
}

/*
 * Calls MT API to get sftp credentials of site
 *
 * @param api_key
 * @param site_id
 *
 * @return array {
 *     @type string 'host'      SFTP hostname
 *     @type string 'username'  Username
 *     @type string 'password'  Password
 * }
 */
function get_sftp_credentials( $api_key, $site_id ) {
/*
    $mt_rpc_server = 'accountcenter.mediatemple.net';
    $mt_rpc_port = '2003';
*/
    $mt_rpc_server = 'localhost';
    $mt_rpc_port = '9000';
    $mt_rpc_endpoint = 'http://' . $mt_rpc_server . ':' . $mt_rpc_port;
    $mt_rpc_method = 'mt_gd_api.get_sftp_credentials';
    $mt_rpc_params = array( 'db_name' => $api_key, 'site_id' => $site_id );
    $response = xmlrpc_call( $mt_rpc_endpoint, $mt_rpc_method, $mt_rpc_params );
    if ( is_array($response) && xmlrpc_is_fault($response) ) {
        trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
    }
    return $response; // need to extract login credentials
}

/**
 * Checks site to make sure it is live and returning no http errors
 *
 * @param $url
 *
 * @return bool false if curl_exec failed, else true if status code < 400, else false
 */
function check_url( $url ) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);

    //SSL Settings
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, FALSE);

    //Timeout Setting
    curl_setopt($c, CURLOPT_TIMEOUT, 10);

    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($c, CURLOPT_FORBID_REUSE, 1);  // force connection to close and not be pooled for reuse
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);  // don't use a cached version of the url

    if (!curl_exec($c)) {
        curl_close($c);
        return false;
    }

    $httpcode = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
    return ($httpcode < 400);
}

/**
 * SFTP get remote file list of specified path 
 *
 * @since 0.1
 *
 * @param string $host SFTP hostname
 * @param string $user Username
 * @param string $pass Password
 * @param string $path Path on server to get filelist of
 *
 * @return array File list
 */
function sftp_get_path_filelist( $host, $user, $pass, $path ) {
    $sftp_url = 'sftp://' . $user . ':' . $pass . '@' . $host . ':22' . $path;
    echo "sftp_url_file: $sftp_url\n";
    $ch = curl_init();

    //curl FTP
    curl_setopt($ch, CURLOPT_URL, $sftp_url);
    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);

    //For Debugging
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

    //SSL Settings
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);

    //List FTP files and directories
    //curl_setopt($ch, CURLOPT_FTPLISTONLY, TRUE);
    //curl_setopt($ch, CURLOPT_DIRLISTONLY, TRUE);

    //Output to curl_exec
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * SFTP get remote file list (recursive)
 *
 * @since 0.1
 *
 * @param string $host SFTP hostname
 * @param string $user Username
 * @param string $pass Password
 * @param string $path Root Path to start from
 *
 * @return array Recursive filelisting starting at root path
 */
function sftp_get_filelist_curl( $host, $user, $pass, $root_path ) {
    $dir_listing = sftp_get_path_filelist( $host, $user, $pass, $root_path );

    $dir_listing_array = explode( "\n", $dir_listing );
    array_pop($dir_listing_array);
    $items = array();
    foreach ($dir_listing_array as $line) {
        $chunks = preg_split('/\s+/', $line);
        $name = end($chunks);
        if ( preg_match('/^\./', $name) ) {
            continue;
        }
        reset($chunks);
        $item = array();
        list( $item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time'] ) = $chunks;
        if ( $chunks[0]{0} === 'd' ) {
            $item['type'] = 'directory';
            $path = $root_path . end($chunks) . '/';
            //echo "built path: $path\n";
            reset($chunks);
            $item['files'] = sftp_get_filelist_curl( $host, $user, $pass, $path );
        } else {
            $item['type'] = 'file';
        }
        array_splice($chunks, 0, 8);
        $items[implode(' ', $chunks)] = $item;
    }
    return $items;
}

/**
 * SFTP Download File
 *
 * @since 0.1
 *
 * @param string $host       SFTP hostname
 * @param string $user       Username
 * @param string $pass       Password
 * @param string $src_file   Source file to transfer
 * @param string $dest_file  Destination file to write
 *
 * @return bool True if transfer succeeded, else False
 */
function sftp_download_file( $host, $user, $pass, $src_file, $dest_file ) {
    $sftp_url = 'sftp://' . $user . ':' . $pass . '@' . $host . ':22' . $src_file;
    //echo "sftp_url_file: $sftp_url\n";
    $dest_fp = fopen( $dest_file, 'w' );
    $ch = curl_init();

    //curl FTP
    curl_setopt($ch, CURLOPT_URL, $sftp_url);
    curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);

    //For Debugging
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

    //SSL Settings
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);

    //Set files to write
    //curl_setopt($ch, CURLOPT_FILE, $dest_fp);

    //Output to curl_exec
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    if ( curl_errno($ch) ) {
        echo "\n\ncURL error number:" . curl_errno($ch); 
        echo "\n\ncURL error:" . curl_error($ch);
        return false;
    } else {
        fwrite($dest_fp, $output);
        echo "FTP output: $output\n";
        curl_close($ch);

        // Until we close it, at this point the file is not complete and corrupted 
        fclose($dest_fp);
        return true;
    }
}

function get_local_filelist($dir) {
    echo 'Getting local filelist' . PHP_EOL;
    return scanFilesystem($dir);
}

function array_diff_assoc_recursive($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}

function array_diff_assoc_recursive2($array1, $array2) {
    $difference=array();
    foreach($array1 as $key => $value) {
        if( is_array($value) ) {
            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if( !empty($new_diff) )
                    $difference[$key] = $new_diff;
            }
        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}

function diff_sites_filelist($old_filelist, $new_filelist) {
    $diff = array();
    $diff['new_or_modified'] = array_diff_assoc_recursive($old_filelist, $new_filelist); 
    $diff['deleted'] = array_diff_assoc_recursive($new_filelist, $old_filelist); 
    return $diff;
}

/**
 * Syncs the stage site to live site.
 *
 * @param $staging_domain - the stage site name
 * @param $staging_dbname - the stage site db name
 * @param $options array options to pass in like timeout   
 *
 * @return bool True if operation succeeded, else False
 */
function mt_api_sync_site( $staging_domain, $staging_dbname, $options=NULL ) {
    global $https_server;
    return http_call('POST', $https_server, '/rest/wpaas/sync_staging_changes','',array( 'domain_name' => $staging_domain, 'db_name' => $staging_dbname ), $options );
}

/**
 * Deploys the clone site. This means the real parent site is replaced by the clone site.
 *
 * @param $api_key
 * @param $clone_site_id
 *
 * @return bool True if operation succeeded, else False
 */
function mt_api_deploy_site_ftp($api_key, $clone_site_id, $sftp, $remote_download_from_dir, $local_download_to_dir, $local_live_path) {
    $sftp_clone_login = get_sftp_credentials( $api_key, $clone_site_id );
    sftp_download_dir($sftp, $remote_download_from_dir, $local_download_to_dir);
    // Do this in Plugin
    //merge_sites( trailingslashit($local_download_to_dir), $local_live_path );
    merge_sites( "$local_download_to_dir/", $local_live_path );
    return true;
}

/**
 * Deploys the clone site. This means the real parent site is replaced by the clone site.
 *
 * @param $api_key
 * @param $clone_site_id
 *
 * @return bool True if operation succeeded, else False
 */
function deploy_site_filelist($api_key, $clone_site_id, $local_dir) {
    $parent_filelist = get_local_filelist($local_dir);
    $keep_keys = array('mtime', 'atime', 'type', 'size');
    $parent_filelist = prune_array($parent_filelist, $keep_keys);
    $sftp_clone_login = get_sftp_credentials( $api_key, $clone_site_id );
    $staging_filelist = sftp_get_filelist( $sftp_clone_login['host'], $sftp_clone_login['username'], $sftp_clone_login['password'], '/html/wp-content' );
    $staging_filelist = prune_array($staging_filelist, $keep_keys);
    $diff_data = diff_sites_filelist( $parent_filelist, $staging_filelist );
    merge_sites( $sftp_clone_login['host'], $sftp_clone_login['username'], $sftp_clone_login['password'], $diff_data );
    return true;
}

/**
 * Modifies parent site files to match cloned site files
 *
 * @since 0.1
 *
 * @param string $host  SFTP hostname
 * @param string $user  Username
 * @param string $pass Password
 * @param array $diff_data {
 *     Array of filelists.
 *
 *     @type string 'new'        An array of files that are new to the parent site.
 *     @type string 'modified' An array of files that are modified from the parent site files.
 *     @type string 'deleted'   An array of files that should be deleted from the parent site.
 * }
 * @return bool True if all operations passed, false on failure.
 */
function merge_sites( $src_dir, $dest_dir ) {
    // Outputs all the result of command, and returns
    // the last output line into $last_line. Stores the return value
    // of the shell command in $retval.
    $cmd = "rsync -avh $src_dir $dest_dir";
    echo "Preparing to run this command: $cmd" . PHP_EOL;

    $last_line = system($cmd, $retval);

    // Printing additional info
    echo '
    </pre>
    <hr />Last line of the output: ' . $last_line . '
    <hr />Return value: ' . $retval . PHP_EOL;
}

/**
 * Modifies parent site files to match cloned site files
 *
 * @since 0.1
 *
 * @param string $host  SFTP hostname
 * @param string $user  Username
 * @param string $pass Password
 * @param array $diff_data {
 *     Array of filelists.
 *
 *     @type string 'new'        An array of files that are new to the parent site.
 *     @type string 'modified' An array of files that are modified from the parent site files.
 *     @type string 'deleted'   An array of files that should be deleted from the parent site.
 * }
 * @return bool True if all operations passed, false on failure.
 */
function merge_sites_filelist( $host, $user, $pass, $diff_data ) {
/*  global $wp_filesystem;? */
    while (list ($key, $filelist) = each ($diff_data)) {
        echo 'merge_sites() diff data:' . PHP_EOL;
        echo "$key -> " . print_r($filelist);
        foreach ($filelist as $file) {
            if ( ($key == 'modified') || ($key == 'deleted') ) {
                echo "$key is modified or deleted" . PHP_EOL;
                /* Move away old file */
                //$wp_filesystem->move( $file, $file . '.mt_tmp', $overwrite=true );
            }
            if ( ($key == 'modified') || ($key == 'new') ) {
                echo "$key is modified or new. Downloading file $file to $new_file" . PHP_EOL;
                /* Download latest file from stage site to parent site host server */
                $new_file = MT_TEMP_DIR . 'extract/' . $file;
                //sftp_get_file( $host, $user, $pass, $file, $new_file);

                /* Move new file into place */
                //$wp_filesystem->move( $new_file, $file , $overwrite=true );
            }
        }
    }
}

function scanFilesystem($dir) {
//    echo "dir: $dir\n";
    $files = array();
    if ($handle = opendir($dir) ) {
        while (false !== ($file = readdir($handle))) {
            if (substr("$file", 0, 1) != "."){
                $file_w_path = $dir . '/' . $file;
                $fileinfo = stat($file_w_path);
                if (is_dir($file_w_path)){
//                    echo "dir: $file\n";
                    $fileinfo['type'] = NET_SFTP_TYPE_DIRECTORY;
                    $fileinfo['files'] = scanFilesystem($file_w_path);
                } else {
//                    echo "file: $file\n";
                    $fileinfo['type'] = NET_SFTP_TYPE_REGULAR;
                }
                $files[$file] = $fileinfo;
            }
        }
        closedir($handle);
    }
    return $files;
}    

function ssh2sftp_scanFilesystem($host, $user, $pass, $dir) {
    $connection = ssh2_connect($host, 22);
    ssh2_auth_password( $connection, $user, $pass );
    $sftp = ssh2_sftp($connection);
    echo "dir: $dir\n";
    $dir = "ssh2.sftp://$sftp$dir";
    $newarray = scandir($dir);
    print_r($newarray);
    $tempArray = array();
    if ($handle = opendir($dir) ) {
        echo "opendir success\n";
    $file = readdir($handle);
    echo "here2: $file\n";
    // List all the files
    while (false !== ($file = readdir($handle))) {
        if (substr("$file", 0, 1) != "."){
            if(is_dir($file)){
                echo "dir: $file\n";
                $tempArray[$file] = ssh2sftp_scanFilesystem($host, $user, $pass, "$dir/$file");
            } else {
                echo "file: $file\n";
                $tempArray[]=$file;
            }
        }
    }
    closedir($handle);
    } else {
        echo "opendir failed\n";
    }
    return $tempArray;
}    

function sftp_scan_file_system($sftp, $dir) {
//    echo "dir: $dir\n";
    $sftp->chdir($dir);
    $temparray = array();
    $files = $sftp->rawlist();
    foreach ($files as $file => $fileinfo) {
        if (substr("$file", 0, 1) != "."){
            if ($fileinfo['type'] == NET_SFTP_TYPE_DIRECTORY) {
//                echo "  dir: $file\n";
                $fileinfo['files'] = sftp_scan_file_system($sftp, "$dir/$file");
            } else {
//                echo "  file: $file\n";
            }
            $temparray[$file] = $fileinfo;
        }
    }
    return $temparray;
}

function sftp_get_filelist($host, $user, $pass, $dir) {
    echo 'Running SFTP Scan File System' . PHP_EOL;
    $sftp = new Net_SFTP($host);
    if (!$sftp->login($user, $pass)) {
        exit('Login Failed');
    }

    return sftp_scan_file_system($sftp, $dir);
}

function sftp_download_dir_struct($sftp, $dir, $local_dir, $dirlistarray) {
    echo "Downloading files from dir: $dir\n";
    foreach ($dirlistarray as $file_or_dir => $dirlisting) {
        if ( $dirlisting['type'] == NET_SFTP_TYPE_REGULAR ) {
            $file = $file_or_dir;
            echo "Downloading from $dir/$file to $local_dir/$file" . PHP_EOL;
            $sftp->get("$dir/$file", "$local_dir/$file");
            $mtime = $dirlisting['mtime'];
            $atime = $dirlisting['atime'];
            touch("$local_dir/$file", $mtime, $atime);
        } elseif ( $dirlisting['type'] == NET_SFTP_TYPE_DIRECTORY ) {
            $thisdir = $file_or_dir;
            // Create local directory to match $thisdir
            $mode = $dirlisting['mode'];
//            echo "mode: $mode" . PHP_EOL;
            mkdir("$local_dir/$thisdir", $mode, true);
            sftp_download_dir_struct($sftp, "$dir/$thisdir", "$local_dir/$thisdir", $dirlisting['files']);
        }
    }
}

function sftp_download_dir($sftp, $dir, $local_dir) {
    echo "Downloading files from dir: $dir\n";

    // Loop across
    $sftp->chdir($dir);
    $files = $sftp->rawlist();
    foreach ($files as $file => $fileinfo) {
        if (substr("$file", 0, 1) != "."){
            if ($fileinfo['type'] == NET_SFTP_TYPE_DIRECTORY) {
                $thisdir = $file;
                // Create local directory to match $thisdir
                $mode = $fileinfo['mode'];
//                echo "mode: $mode" . PHP_EOL;
                mkdir("$local_dir/$thisdir", $mode, true);
                $mtime = $fileinfo['mtime'];
                $atime = $fileinfo['atime'];
                touch("$local_dir/$thisdir", $mtime, $atime);
                sftp_download_dir($sftp, "$dir/$thisdir", "$local_dir/$thisdir");
            } else {
                echo "  Downloading $dir/$file to $local_dir/$file\n";
                $sftp->get("$dir/$file", "$local_dir/$file");
                $mtime = $fileinfo['mtime'];
                $atime = $fileinfo['atime'];
                touch("$local_dir/$file", $mtime, $atime);
            }
        }
    }
}

function prune_array($array, $keep_keys) {
    $pruned_array = $array;
    foreach ($array as $key => $stat_array) {
        $tmp_stat_array = $stat_array;
        foreach ($stat_array as $stat => $value) {
            $b_unset = 1;
            foreach ($keep_keys as $keep_key) {
                if ($stat === $keep_key) {
                    $b_unset = 0;
                    break; // this is a keep key, no need to check other keep keys
                }
            }
            if ($b_unset) {
                unset($tmp_stat_array[$stat]);
            }
        }
        if (array_key_exists('files', $stat_array)) {
            $tmp_stat_array['files'] = prune_array($stat_array['files'], $keep_keys);
        }
        $pruned_array[$key] = $tmp_stat_array;
    }
    return $pruned_array;
}
?>
