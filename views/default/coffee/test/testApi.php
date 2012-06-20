<?php
foreach ($vars['exposed'] as $call) {
    echo "<h1>" . $call['method'] . "</h1>";
    echo $call['comment'];
    echo '<form class="testApi" action="/services/api/rest/default/" enctype="multipart/form-data" method="' . $call['call_method'] . '">';
    foreach ($call['params'] as $name => $attributes) {
        echo "<p><label>" . $name . "</label>" . elgg_view('input/text' , array('name' => $name, 'size' =>'30px', 'value'=>'')) . '</p>';
    }
    if (in_array($call['method'], array('coffee.uploadData','coffee.uploadUserAvatar', 'coffee.uploadUserCover'))) {
        switch ($call['method']) {
            case 'coffee.uploadData':
                $name = 'upload';
                break;
            case 'coffee.uploadUserAvatar':
                $name = 'avatar';
                break;
            case 'coffee.uploadUserCover':
                $name = 'cover';
                break;
            default:
                break;
        }
        echo elgg_view('input/file' , array('name' => $name, 'size' =>'30px'));
    }
    echo elgg_view('input/hidden', array('name' => 'method', 'value' => $call['method']));
    echo '<p>' . elgg_view('input/reset');
    echo elgg_view('input/submit') . '</p><br />';
    echo '</form>';
}
?>
<style>
    .testApi {
        display: none;
    }
    h1 {
        cursor: pointer;
    }
</style>
<script>
    $('h1').click(function () {
        $(this).next('form').slideToggle();
    });
</script>