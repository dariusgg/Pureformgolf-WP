<?php include "_panel-css.php";?>
<div class="wrap">
    <h2><?php echo __( 'Debug List' , 'sfcounter' ); ?></h2>
    <br />

    <form action="" method="post" id="SocialFansSocialForm">
        <div id="poststuff">
            <div id="post-body">
                <div id="post-body-content" class="">
                    <table class="wp-list-table widefat fixed striped posts">
                        <thead>
                        <tr>
                            <td style="width: 15%;"><?php echo __( 'Date', 'sfcounter' ); ?></td>
                            <td style="width: 10%;"><?php echo __( 'Social', 'sfcounter' ); ?></td>
                            <td style="width: 30%;"><?php echo __( 'Error Message', 'sfcounter' ); ?></td>
                            <td style="width: 45%;"><?php echo __( 'Solution', 'sfcounter' ); ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $list as $row ) :?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', $row['3']); ?></td>
                                <td><?php echo $row['0']; ?></td>
                                <td><?php echo $row['1']; ?></td>
                                <td><?php echo $row['2']; ?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <br />
                    <input type="submit" value="clear" class="button-primary" name="sfcounter-debug-clear" />
                </div><!-- End post-body-content -->
            </div><!-- End post-body -->
        </div><!-- End poststuff -->
    </form><!-- End Form -->
</div><!-- End Wrap -->