<?php if(isset($_GET['msg']) && $_GET['msg']==1):?>
	<div class="updated below-h2" id="message"><p>Shortcode added successfully</p></div>
<?php elseif(isset($_GET['msg']) && $_GET['msg']==2):?>
	<div class="updated below-h2" id="message"><p>Record deleted successfully !</p></div>
<?php endif;?>
<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
<h2>ECT Buy Button <a class="add-new-h2" href="admin.php?page=add_ect">Add New</a></h2>
<?php 
	global $wpdb;
	$Ch=$wpdb->get_results("select * from ".$wpdb->prefix."options where option_name like 'shortcode_ec_%' order by option_id desc");
?>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th>Product ID</th>
				<th>Shortcode</th>
				<th>Actions</th>
			</tr>
		</thead>
		<?php if(!empty($Ch)):?>
			<?php foreach($Ch as $s):?>
			<tr>
				<td><?php $p=explode('_',$s->option_name);
				echo end($p);?></td>
				<td><code>[view_add_to_cart id=<?php echo end($p)?>]</code>
				</td>
				<td><a href="javascript:void(0)" onclick="del(<?php echo $s->option_id?>)"><img src="<?php echo plugin_dir_urL(__FILE__)?>img/delete.png" alt="Delete" title="Delete"/></a></td>
			</tr>
			<?php endforeach;?>
		<?php else:?>
			<tr><td colspan="3" align="center">No record found !</td></tr>
		<?php endif;?>
		<tfoot>
			<tr>
				<th>Product ID</th>
				<th>Shortcode</th>
				<th>Actions</th>
			</tr>
		</tfoot>
	</table>	
	<script type="text/javascript">
		function del(id)
		{
			var res=confirm('Do you really want to  delete this ?');
			if(res)
			window.location="admin.php?page=ec&act=del&id="+id;
		}
	</script>
<?php
if(isset($_GET['act']) && $_GET['act']=='del')
{
	global $wpdb;
	$wpdb->query("delete from ".$wpdb->prefix."options where option_id='".$_GET['id']."'");
	echo '<script>window.location="admin.php?page=ec&msg=2"</script>';
}	
echo do_shortcode('[view_add_to_cart id=pc002]');
?>