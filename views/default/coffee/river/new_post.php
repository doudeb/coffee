<?php

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->title);
$excerpt = elgg_get_excerpt($excerpt);

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'message' => $excerpt,
));
