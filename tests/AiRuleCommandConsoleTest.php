<?php

namespace Salehhashemi\LaravelIntelliDb\Tests;

use Illuminate\Foundation\Console\Kernel;
use Orchestra\Testbench\TestCase;
use Salehhashemi\LaravelIntelliDb\Console\AiRuleCommand;
use Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider;

class AiRuleCommandConsoleTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [LaravelIntelliDbServiceProvider::class];
    }

    /** @test */
    public function test_ai_rule_command_is_registered()
    {
        $kernel = $this->app->make(Kernel::class);

        $commandList = $kernel->all();

        $this->assertArrayHasKey('ai:rule', $commandList);
    }

    /** @test */
    public function test_ai_rule_command_options()
    {
        $command = $this->app->make(AiRuleCommand::class);
        $definition = $command->getDefinition();
        $options = $definition->getOptions();
        $arguments = $definition->getArguments();

        $this->assertArrayHasKey('name', $arguments);
        $this->assertArrayHasKey('description', $options);
    }
}
