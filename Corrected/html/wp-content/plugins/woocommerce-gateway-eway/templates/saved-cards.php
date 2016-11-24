<h2 id="saved-cards" style="margin-top:40px;"><?php _e( 'Saved cards', 'wc-eway' ); ?></h2>
<table class="shop_table">
	<thead>
		<tr>
			<th><?php _e( 'Card', 'wc-eway' ); ?></th>
			<th><?php _e( 'Expires', 'wc-eway' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( (array)$cards as $card ) : ?>
		<tr>
            <td><?php echo $card['number']; ?></td>
            <td><?php printf( __( 'Expires %s/%s', 'wc-eway' ), $card['exp_month'], $card['exp_year'] ); ?></td>
			<td>
                <form action="" method="POST">
                    <?php wp_nonce_field ( 'eway_del_card' ); ?>
                    <input type="hidden" name="eway_delete_card" value="<?php echo esc_attr( $card['id'] ); ?>">
                    <input type="submit" class="button" value="<?php _e( 'Delete card', 'wc-eway' ); ?>">
                </form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>