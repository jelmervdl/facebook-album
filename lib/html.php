<?php

function _html($data)
{
	return htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
}

function _attr($data)
{
	return htmlentities($data, ENT_QUOTES, 'UTF-8');
}
