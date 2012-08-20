<?php

function include_template($__template__, array $__variables__)
{
	extract($__variables__);

	ob_start();
	include $__template__;
	return ob_get_clean();
}