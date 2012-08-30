<?php
$options  = array('types'=>'object','subtypes'=>'file','limit'=>1);
$options['joins']   = array("Inner join {$GLOBALS['CONFIG']->dbprefix}objects_entity obj_ent On e.guid = obj_ent.guid");
$options['wheres']   = array("obj_ent.title = 'logo'");
$site_logo = elgg_get_entities($options);
$options['wheres']   = array("obj_ent.title = 'background'");
$site_background = elgg_get_entities($options);

?>
<p>
    <label for="logo">Logo image</label>
<?php
echo elgg_view('input/file', array(
    'name' => 'logo'
    ,'value' => ''
));
?>
</p>
<?php if($site_logo[0] instanceof ElggFile) echo elgg_view('output/img', array('src' => 'file/download/' . $site_logo[0]->guid, 'width' => '100px')); ?>
<p>
    <label for="background">Background image</label>
<?php
echo elgg_view('input/file', array(
    'name' => 'background'
    ,'value' => ''
));
?>
</p>
<?php if($site_background[0] instanceof ElggFile)  echo elgg_view('output/img', array('src' =>  'file/download/' . $site_background[0]->guid, 'width' => '300px')); ?>
<script>
    formElm = $('form#coffee-settings');
    formElm
        .attr( "enctype", "multipart/form-data" )
        .attr( "encoding", "multipart/form-data" );
</script>