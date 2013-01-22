<?php
if (is_array($_FILES)) {
    foreach ($_FILES as $name=>$values) {
        //var_dump($values) . die();
        if ($values["error"]==0) {
            $file = new FilePluginFile();
            $file->subtype = "file";
            $file->title = $name;
            $file->access_id = COFFEE_DEFAULT_ACCESS_ID;
            $prefix = "file/";
            $filestorename = elgg_strtolower(time().$values['name']);
            $mime_type = $file->detectMimeType($values['tmp_name'], $values['type']);
            $file->setFilename($prefix . $filestorename);
            $file->setMimeType($mime_type);
            $file->originalfilename = $values['name'];
            $file->simpletype = file_get_simple_type($mime_type);
            // Open the file to guarantee the directory exists
            $file->open("write");
            $file->close();
            move_uploaded_file($values['tmp_name'], $file->getFilenameOnFilestore());
            $guid = $file->save();
        }
    }
}
system_message(elgg_echo('plugins:settings:save:ok', array('coffee')));
forward(REFERER);
?>
