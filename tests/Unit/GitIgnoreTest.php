<?php

namespace Tests\Unit;

use Tests\Traits\CleansOutput;
use Nonetallt\Jinitialize\Testing\TestCase;

class GitIgnoreTest extends TestCase
{
    use CleansOutput;

    private $output;
    private $expected;
    private $file;

    public function testGitignoreDoesNotExistByDefault()
    {
        $this->assertFalse(file_exists($this->output.'/gitignore'));
    }

    public function testGitignoreIsCreatedIfItDoesNotExist()
    {
        $this->runCommand("git:init $this->output");
        $this->runCommand('git:ignore test');

        $this->assertTrue(is_file($this->file));
    }

    public function testLineIsAddedToGitignore()
    {
        $this->runCommand("git:init $this->output");
        $this->runCommand('git:ignore test');

        $this->assertEquals('test', file_get_contents($this->file));
    }

    public function testLinesAreAppendedToFile()
    {
        $this->runCommand("git:init $this->output");
        $this->runCommand('git:ignore test1');
        $this->runCommand('git:ignore test2');

        $this->assertEquals('test1'.PHP_EOL.'test2', file_get_contents($this->file));
    }

    public function testRevertRemovesLineIfFileWasNotCreated()
    {
        $this->runCommand("git:init $this->output");
        $this->runCommand('git:ignore test');
        $tester = $this->runCommand('git:ignore test1');
        $this->runCommand('git:ignore test');
        $tester->getCommand()->revert();

        /* Assert that only the middle line 'test1' that was reverted is gone */
        $this->assertEquals('test'.PHP_EOL.'test', file_get_contents($this->file));
    }

    public function testRevertRemovesFileIfItWasCreated()
    {
        $this->runCommand("git:init $this->output");
        $tester = $this->runCommand('git:ignore test');
        $tester->getCommand()->revert();

        $this->assertFalse(is_file($this->file));
    }

    public function setUp()
    {
        parent::setUp();
        $this->registerLocalPlugin(__DIR__.'/../../composer.json');
        $this->cleanOutput();

        /* Create subfolder for tests inside output */
        $this->output = $this->outputFolder('git-test');
        mkdir($this->output);

        $this->file = $this->output . '/.gitignore';
        $this->expected = $this->expectedFolder('.gitignore');
    }
}
