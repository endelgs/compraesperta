<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<label>
		<?php 
			global $wp_query;
			if(is_tax('marca')){
				echo "<input type='hidden' name='marca' value='{$wp_query->query_vars['marca']}' />";
			}
			if(is_tax('departamento')){
				echo "<input type='hidden' name='departamento' value='{$wp_query->query_vars['departamento']}' />";
			}
		?>
		
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'store' ) ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder', 'store' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'store' ) ?>" />
	</label>
	<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
</form>