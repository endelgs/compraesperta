<style>
	.add_prod li label
	{
		width:150px;
		display:inline-block;
		font-weight:bold;
	}
	.add_prod li
	{
		line-height:36px;
	}
	.add_prod li input[type=text]
	{
		width:260px;
	}
	.btn
	{
		font-size: 20px !important;
		height: 40px !important;
		line-height: 40px !important;
		padding: 0 12px 2px !important;
		width: 98px !important;
	}
	</style>
	<h2>ECT Buy Button</h2>
	<form method="post">
		<ul class="add_prod">
			<li>
				<label>Product ID:</label>
				<input type="text" name="prod_id"/>
			</li>
			<li>
				<label>Image or submit button:</label>
				<input type="radio" name="btn_type" value="img" checked class="rdo"/>An image
				&nbsp;
				<input type="radio" name="btn_type" value="txt" class="rdo"/>Submit button
			</li>
			<li class="hid_clsn" style="display:none;">
				<label>CSS class name:</label>
				<input type="text" name="class_name" class="hid_clsn" style="display:none;" />
			</li>
			<li class="hid_clsn" style="display:none;">
				<label>Submit button Text</label>
				<input type="text" name="valn" class="hid_clsn" style="display:none;" />
			</li>
			<li><input type="submit" accesskey="p" value="Save" class="btn button-primary button-large" id="publish" name="publish"></li>
		</ul>
	</form>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script type="text/javascript">
		$('.rdo').click(function(){
			if($(this).val()=='txt')
				$('.hid_clsn').show();
			else
				$('.hid_clsn').hide();
		});
	</script>
<?php
if(!empty($_POST))
	{
		global $wpdb;
		$s='<form method="post" action="/cart.php">';
			$s.='<input type="hidden" name="id" value="'.$_POST['prod_id'].'" />';
			$s.='<input type="hidden" name="mode" value="add" />';
			if($_POST['btn_type']=='img')
				$s.='<input type="image" src="/images/buy.gif" border="0" />';
			elseif($_POST['btn_type']=='txt')
			{	
				$cl='';
				$vl='Add to cart';
				if(!empty($_POST['class_name']))
					$cl="class='".$_POST['class_name']."'";
				if(!empty($_POST['valn']))
					$vl=$_POST['valn'];
				$s.='<input type="submit" '.$cl.' name="'.$vl.'" value="'.$vl.'">';
			}
		$s.='</form>';
		update_option('shortcode_ec_'.$_POST['prod_id'],addslashes($s));
		echo '<script>window.location="admin.php?page=ec&msg=1"</script>';
	}
?>	