<?php

/**
 * Group images
 *
 */
 
$container_guid = (int)get_input('guid');
elgg_set_page_owner_guid($container_guid);
group_gatekeeper();
$container = get_entity($container_guid);
if(!$container || !(elgg_instanceof($container, 'group'))) {
    return;
}

$db_prefix = elgg_get_config('dbprefix');
$filter = '';

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('photos'), 'photos/siteimagesall');
elgg_push_breadcrumb($container->name);

$offset = (int)get_input('offset', 0);
$max = 20;

// grab the html to display the most recent images
$images = elgg_list_entities(array('type' => 'object',
                                  'subtype' => 'image',
                                  'owner_guid' => NULL,
                                  'joins' => array("join {$db_prefix}entities u on e.container_guid = u.guid"),
                                  'wheres' => array("u.container_guid = {$container_guid}"),
                                  'order_by' => "e.time_created desc",
                                  'limit' => $max,
                                  'offset' => $offset,
                                  'full_view' => false,
                                  'list_type' => 'gallery',
                                  'gallery_class' => 'tidypics-gallery'
                                ));

$title = elgg_echo('tidypics:siteimagesgroup', array($container->name));

elgg_load_js('lightbox');
elgg_load_css('lightbox');
/* Delete Tani 2013.07.01 */
/*
elgg_register_menu_item('title', array('name' => 'addphotos',
                                       'href' => "ajax/view/photos/selectalbum/?owner_guid=$container_guid",
                                       'text' => elgg_echo("photos:addphotos"),
                                       'class' => 'elgg-lightbox',
                                       'link_class' => 'elgg-button elgg-button-action'));
*/

if (!empty($images)) {
        $area2 = $images;
} else {
        $area2 = elgg_echo('tidypics:siteimagesgroup:nosuccess');
}
$body = elgg_view_layout('content', array(
        'filter_override' => $filter,
        'content' => $area2,
        'title' => $title,
        'sidebar' => elgg_view('photos/sidebar', array('page' => 'owner')),
));

// Draw it
echo elgg_view_page($title, $body);
