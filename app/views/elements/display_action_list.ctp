					<?php
						if (isset($msg))
						echo $msg;
						foreach ( $controllerPerms as $controller => $actions )
						{
							echo "<div id=\"allowedDiv\"><h2>Allowed Actions</h2><p>Click an item to Deny it.</p>";
							foreach ( $actions as $key => $action )
							{
								if ($action == 1)
								{
									echo "<a href=\"#\" class=\"allowedActions action-{$key}\">" . Inflector::humanize($key) . "</a><br />\n";
								}
							}
							echo "</div>\n";
							echo "<div id=\"deniedDiv\"><h2>Denied Actions</h2><p>Click an item to Allow it.</p>";
							foreach ( $actions as $key => $action )
							{
								if ($action == 0)
								{
									echo "<a href=\"#\" class=\"deniedActions action-{$key}\">" . Inflector::humanize($key) . "</a><br />\n";
								}
							}
							echo "</div>\n";
						}
					?>
<script type="text/javascript">
//<![CDATA[
	$(document).ready(function() {
		$('a.allowedActions').click(function()
		{
			$.ajax({
				url:
					"http://localhost/cake_multi_acl_13/users/acl_set/"+$group_id+"/"+$('#controller').val()+"/"+$(this).semantic('action')+"/deny",
				success:
					function(html)
					{
						$("#display_actionList").html(html);
					},
				error:
					function(object, text, error)
					{
						$("#display_action_list").html(text);
					}
			});
		});
		$("a.allowedActions").hover(function () {
			$(this).addClass("hilite");
		}, function () {
			$(this).removeClass("hilite");
		});
		$('a.deniedActions').click(function()
		{
			$.ajax({
				url:
					"http://localhost/cake_multi_acl_13/users/acl_set/"+$group_id+"/"+$('#controller').val()+"/"+$(this).semantic('action')+"/allow",
				success:
					function(html)
					{
						$("#display_action_list").html(html);
					},
				error:
					function(object, text, error)
					{
						$("#display_actionList").html(text);
					}
			});
		});
		$("a.deniedActions").hover(function () {
			$(this).addClass("hilite");
		}, function () {
			$(this).removeClass("hilite");
		});
	});2

	// Function to extract DOM values semantically
	$.fn.semantic = function(key) {
		var r = [];
		this.each(function() {
			var $this = $(this);
			r.push($this.attr('class').match(new RegExp(key+'-([^ ]+)'))[1]);
		});
		return r.length == 1
			? r[0]
			: r;
	};

//]]>
</script>