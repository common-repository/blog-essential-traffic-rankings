<?php

	if (!defined('ABSPATH')) exit;
	
	if (isset($_GET['page']) && $_GET['page'] == 'setting' && get_option('bpetr_is_auth') != 1) {
	    header('Location:' . 'admin.php?page=blog-essential-traffic-rankings');
	}