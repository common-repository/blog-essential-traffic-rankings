<?php

	if (!defined('ABSPATH')) exit;
	
	if (isset($_GET['pp-success']) && $_GET['pp-success'] == '1') {
	?>
		<div id="message" class="updated">
			Thank you for our order!<br />
			Premium will be activated shortly and you’ll get a confirmation email.<br />
			Your order will appear in the “My Subscription” page.
		</div>
	<?php
	} elseif (isset($_GET['pp-sub-cancel']) && $_GET['pp-sub-cancel'] == '1') {
	?>
		<div id="message" class="updated">Your subscription has been canceled!</div>
	<?php
	} elseif (isset($_GET['pp-txn-error']) && $_GET['pp-txn-error'] == '1') {
	?>
		<div id="message" class="error">
			There was an error while saving your transaction.<br />
			Please <a href="mailto:plugin@bloggerplot.com" target="_blank">let us know</a> about your issue.
		</div>
	<?php
	}
