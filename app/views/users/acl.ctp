<?php
if(isset($groups))
{

	foreach ($groups as $group)
	{
		echo "<div class=\"block\">";
		echo "<span>" . $group['Group']['name'] . "</span>";
		echo $group['Group']['description'] . "<br />";
		echo "<h3>" . $html->link('View', '/users/acl/'. $group['Group']['id']). "</h3>";
		echo "</div>";
	}
}
if(isset($controllerList))
{

	ksort($controllerList);
	echo '<div class="block">';
	echo "<h2>" . $group['Group']['name'] . " Access Control&nbsp;" . '<span id="loading" style="display: none;">' . $html->image('ajax/ajax-loader.gif', array('alt' => 'Ajax Loading Icon')) . "</span>" ."</h2>";
	$selectOptions = array();
	foreach ($controllerList as $controller => $action)
	{
		$selectOptions[$controller] = $controller;
	}
	echo $form->input('controller', array('options' => $selectOptions, 'type' => 'select', 'label' => 'Select a Controller: &nbsp;'));
	echo '<div id="display_action_list">';
	echo $this->element('display_action_list');
	echo "</div>";
	echo "</div>";
}
?>
<script type="text/javascript">
//<![CDATA[
	$(document).ready(function() {
		$("#loading").ajaxStart(function()
		{
			$(this).show();
		});
		$("#loading").ajaxStop(function()
		{
			$(this).hide();
		});
		$group_id = <?php echo $group['Group']['id']; ?>;
		$('#controller').change(function()
		{
			$.ajax({
				url: "http://localhost/cake_multi_acl_13/users/display_action_list/"+$(this).val()+"/"+$group_id,
				success:
				function(html)
				{
					$("#display_action_list").html(html);
				}
			});
		});
	});
//]]>
</script>