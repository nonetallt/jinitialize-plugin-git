<?php

namespace Nonetallt\Jinitialize\Plugin\Git\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Bit3\GitPhp\GitRepository;

use Nonetallt\Jinitialize\JinitializeCommand;

class GitSetRemote extends JinitializeCommand
{
    private $path;
    private $url;
    private $name;

    protected function configure()
    {
        $this->setName('set-remote');
        $this->setDescription('Set remote repository url.');
        $this->setHelp('Adds origin remote and sets both the remote fetch and push URL to the given URL.');

        $this->addArgument('remoteUrl', InputArgument::REQUIRED, 'Remote repository URL.');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name of the new remote.', 'origin');
    }

    protected function handle($input, $output, $style)
    {
        $this->path = $this->import('path');
        $this->url = $input->getArgument('remoteUrl');
        $this->name = $input->getArgument('name');

        if(is_null($this->path)) $this->abort("Path to git repository must be set.");

        $git = new GitRepository($this->path);
        $git->remote()->add($this->name, $this->url)->execute();
        $git->remote()->setUrl($this->name, $this->url)->execute();
        $git->remote()->setPushUrl($this->name, $this->url)->execute();

        $this->export('remote_url', $this->path);
    }

    public function revert()
    {
        $git = new GitRepository($this->path);
        $git->remote()->remove($this->name)->execute();
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
