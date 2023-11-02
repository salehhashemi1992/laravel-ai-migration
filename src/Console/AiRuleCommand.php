<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\RuleMakeCommand;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

class AiRuleCommand extends RuleMakeCommand
{
    protected $name = 'ai:rule';

    protected $description = 'Create a new rule using AI';

    public function __construct(
        Filesystem $files,
        private readonly OpenAi $openAi
    ) {
        parent::__construct($files);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'The description of the validation rule');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildClass($name): string
    {
        $ruleDescription = $this->getRuleDescription();

        $prompt = $this->createAiPrompt($ruleDescription);

        $this->info('Generating AI rule, this might take a few moments...');

        try {
            return $this->fetchAiGeneratedContent($prompt);
        } catch (RequestException $e) {
            $this->error('Error fetching AI-generated content: '.$e->getMessage());
        }

        return parent::buildClass($name);
    }

    /**
     * Get the rule description from the option or ask the user if not provided.
     */
    private function getRuleDescription(): string
    {
        $ruleDescription = $this->option('description');

        if (! $ruleDescription) {
            $ruleDescription = $this->ask('Please describe the validation rule you want to generate (e.g., "validate unique email")');
        }

        return $ruleDescription;
    }

    /**
     * Create a prompt to generate the content of the rule file.
     */
    private function createAiPrompt(string $ruleDescription): string
    {
        $ruleName = $this->argument('name');

        if (! is_string($ruleName)) {
            throw new InvalidArgumentException("The 'name' argument must be a string.");
        }

        $prompt = "Generate the PHP code for a Laravel validation rule class named '".$ruleName."' that implements the Rule interface and does the following:";
        $prompt .= "\n$ruleDescription";
        $prompt .= "\nProvide only the final Laravel validation rule class code (include everything like <?php tag and namespace) without any explanations or additional context.";
        $prompt .= "\nInclude type hints for methods and their arguments.";

        return $prompt;
    }

    /**
     * Fetch the AI generated content.
     *
     * @throws RequestException
     */
    private function fetchAiGeneratedContent(string $prompt): string
    {
        return $this->openAi->execute($prompt, 1000);
    }
}
