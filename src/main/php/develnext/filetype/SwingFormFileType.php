<?php

namespace develnext\filetype;

use develnext\editor\SwingFormEditor;
use develnext\project\Project;
use php\io\File;
use php\io\FileStream;
use php\lib\str;
use php\swing\UIContainer;

class SwingFormFileType extends FileType {

    public function onDetect(File $file, Project $project = null) {
        if ($file->exists() && $file->isFile()) {
            $st = new FileStream($file, 'r');
            $test = str::trimLeft($st->read(20));

            $success = str::startsWith($test, '<ui-dialog') || str::startsWith($test, '<ui-form');
            $st->close();

            return $success;
        } else
            return false;
    }

    public function createEditor(UIContainer $container, File $file, Project $project = null) {
        return new SwingFormEditor($container, $file, $project);
    }

    public function getIcon() {
        return 'images/icons/filetype/swing_form.png';
    }
}
