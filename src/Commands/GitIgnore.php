<?php

namespace Nonetallt\Jinitialize\Plugin\Git\Commands;

use Symfony\Component\Console\Input\InputArgument;

use Nonetallt\Jinitialize\JinitializeCommand;

class GitIgnore extends JinitializeCommand
{
    private $ignore;
    private $path;
    private $createdGitignore = false;

    protected function configure()
    {
        $this->setName('ignore');
        $this->setDescription('Add a given line to the project .gitignore.');
        $this->setHelp('If project does not already have a .gitignore, a new one will be created.');
        $this->addArgument('ignore', InputArgument::REQUIRED, 'The line to add to .gitignore.');
    }

    protected function handle($input, $output, $style)
    {
        $this->path = $this->import('projectPath');
        $this->ignore = $input->getArgument('ignore');

        if(is_null($this->path)) $this->abort("Path to git repository must be set.");

        /* Add line to gitignore in project path */
        $this->addLine($this->filepath(), $this->ignore);
    }

    private function addLine(string $filepath, string $line)
    {
        /* If file does not exist, it has to be created */
        if(! file_exists($filepath)) {
            $this->createdGitignore = true;
        }
        else {
            /* Add linebreak before content to write new line */
            $line = PHP_EOL . $line;
        }

        file_put_contents($filepath, $line, FILE_APPEND);
    }

    private function filepath()
    {
        return "$this->path/.gitignore";
    }

    public function revert()
    {
        /* Remove gitignore if it was created */
        if($this->createdGitignore) {
            unlink($this->filepath());
            return;
        }

        /* Get gitignore content */
        $content = file_get_contents($this->filepath());

        /* The content that was added by this command */
        $addedContent = PHP_EOL . $this->ignore;

        /* Remove first instance of added content */
        $content = $this->str_remove_first($content, $addedContent);

        /* Overwrite gitingore with new content */
        file_put_contents($this->filepath(), $content);
    }

    private function str_remove_first(string $content, string $remove)
    {
        /* Find first instance of content */
        $pos = strpos($content, $remove, -0);

        if($pos === false) return $content;

        /* Remove added content */
        return substr($content, 0, $pos) . substr($content, $pos + strlen($remove));
    }
    
    public function requiresExecuting()
    {
        return [
            GitInit::class
        ];
    }

    public function recommendsExecuting()
    {
        return [

        ];
    }

    public function recommendsRoot()
    {
        // bool, wether command should be executed with administrative priviliges
        return false;
    }
}
