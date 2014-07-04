<?php
namespace develnext\ide\std;
use develnext\Manager;
use develnext\tool\GradleTool;
use develnext\tool\JavaTool;
use php\lib\str;
use php\swing\event\ComponentEvent;
use php\swing\UIDialog;

/**
 * Class StandardIdeMenuHandlers
 * @package develnext\ide\std
 */
class StandardIdeMenuHandlers {
    /** @var array */
    protected $handlers = [];

    public function __construct() {
        $elements = [
            'file:open-project', 'file:new-project', 'file:save-all', 'file:exit',
            'edit:undo', 'edit:redo', 'edit:delete',
            'build:run'
        ];

        foreach ($elements as $el) {
            $handler = str::replace($el, ':', '_');
            $handler = str::replace($handler, '-', '');

            $this->handlers[$el] = [$this, $handler];
        }
    }

    public function file_openProject() {

    }

    public function file_newProject() {
        $manager = Manager::getInstance();
        $manager->getSystemForm('project/NewProject.xml')->showModal();
    }

    public function file_saveAll() {
        $manager = Manager::getInstance();
        $manager->currentProject->saveAll();
    }

    public function file_exit() {

    }

    public function edit_undo() {

    }

    public function edit_redo() {

    }

    public function edit_delete() {
        $manager = Manager::getInstance();
        if (UIDialog::confirm(_('Are you sure?'), _('Question'), UIDialog::YES_NO_OPTION) == UIDialog::YES_OPTION) {
            $files = $manager->currentProject->getFileTree()->getSelectedFiles();
            $deleted = [];
            foreach ($files as $file) {
                if ($file->delete()) {
                    $deleted[] = $file;
                }
            }

            foreach ($deleted as $file) {
                $manager->currentProject->updateFile($file);
            }

        }
    }

    public function build_run($e) {
        $manager = Manager::getInstance();

        $gradle = new GradleTool();
        $e->target->enabled = false;

        $manager->ideManager->logTool($gradle, $manager->currentProject->getDirectory(), ['run'], function() use ($e){
            $e->target->enabled = true;
        });
    }

    /**
     * @return array
     */
    public function getHandlers() {
        return $this->handlers;
    }
}