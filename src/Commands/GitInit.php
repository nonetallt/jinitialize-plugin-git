<?php

namespace Nonetallt\Jinitialize\Plugin\Git\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Bit3\GitPhp\GitRepository;

use Nonetallt\Jinitialize\JinitializeCommand;

class GitInit extends JinitializeCommand
{
    private $folder;

    protected function configure()
    {
        $this->setName('init');
        $this->setDescription('This is an example command');
        $this->setHelp('Extended description here');

        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the project folder.');
    }

    protected function handle($input, $output, $style)
    {
        $path = $input->getArgument('path');

        if(! is_dir($path)) $this->abort("Path $path is not a valid folder path");

        $this->folder = $path;
        $git = new GitRepository($path);
        $git->init()->execute();

        $this->export('projectPath', $this->folder);
    }

    public function revert()
    {
        if(!is_null($this->folder)) {
            $this->removeDirectoryContents($this->folder);
            rmdir($this->folder);
        }
    }

    private function removeDirectoryContents(string $dir, int $level = 1)
    {
        if(! is_dir($dir)) return;
        
        $objects = scandir($dir); 

        foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (is_dir($dir."/".$object)) {
                    $this->removeDirectoryContents($dir."/".$object, $level+1);
                }
                else {
                    unlink($dir."/".$object); 
                }
            } 
        }
        if($level > 1) rmdir($dir); 
    }

    public function requiresExecuting()
    {
        return [

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
