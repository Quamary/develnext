<?php
namespace develnext\filetype;

use develnext\project\EditorManager;
use develnext\project\Project;
use php\io\File;
use php\swing\UIContainer;

class DirectoryFileType extends FileType {

    public function onDetect(File $file, Project $project = null) {
        return $file->isDirectory();
    }

    public function createEditor(File $file, EditorManager $manager = null) {
        return null;
    }

    public function getIcon() {
        return "images/icons/filetype/folder.png";
    }
}
