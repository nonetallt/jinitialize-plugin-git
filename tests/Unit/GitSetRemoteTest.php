<?php

namespace Tests\Unit;

use Bit3\GitPhp\GitRepository;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Plugin\Git\Commands\GitInit;

class GitSetRemoteTest extends TestCase
{
    use CleansOutput;

    private $git;
    private $output;

    public function testGitFolderDoesNotExistByDefault()
    {
        $this->assertFalse(is_dir($this->output.'/.git'));
    }

    public function testRemoteIsNotSetByDefault()
    {
        $this->runCommand("git:init $this->output");

        /* Note that getNames() will return origin when .git folder does not exist */
        $this->assertEmpty($this->git->remote()->getNames());
    }

    public function testRemoteURLsAreSet()
    {
        $this->runCommand("git:init $this->output");

        $url = 'https://github.com/nonetallt/jinitialize';
        $this->runCommand("git:set-remote $url");

        $this->assertEquals(['origin'], $this->git->remote()->getNames());
    }

    public function testRevertRemovesTheRemoteURLs()
    {
        $this->runCommand("git:init $this->output");

        $url = 'https://github.com/nonetallt/jinitialize';
        $tester = $this->runCommand("git:set-remote $url");
        $tester->getCommand()->revert();

        $this->assertEmpty($this->git->remote()->getNames());
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();

        /* Create subfolder for tests inside output */
        $this->output = $this->outputFolder('git-test');
        mkdir($this->output);

        /* Create git object from output dir */
        $this->git = new GitRepository($this->output);
    }
}
