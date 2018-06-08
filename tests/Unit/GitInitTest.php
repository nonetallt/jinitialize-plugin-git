<?php

namespace Tests\Unit;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Testing\TestCase;
use Nonetallt\Jinitialize\Plugin\Git\Commands\GitInit;

class GitInitTest extends TestCase
{
    use CleansOutput;

    private $output;

    public function testGitFolderDoesNotExistByDefault()
    {
        $this->assertFalse(is_dir($this->output.'/.git'));
    }

    public function testGitFolderIsCreated()
    {
        $this->runCommand("git:init $this->output");
        $this->assertTrue(is_dir($this->output.'/.git'));
    }

    public function testRevertRemovesTheCreatedFolder()
    {
        $tester = $this->runCommand("git:init $this->output");
        $command = $tester->getCommand();

        $command->revert();
        $this->assertFalse(is_dir($this->output.'/.git'));
    }

    public function testExportsProjectPath()
    {
        $tester = $this->runCommand("git:init $this->output");
        $this->assertContainerContains(['path' => $this->output]);
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();

        /* Create subfolder for tests inside output */
        $this->output = $this->outputFolder('git-test');
        mkdir($this->output);
    }
}
