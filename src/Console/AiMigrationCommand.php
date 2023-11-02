<?php

namespace Salehhashemi\LaravelIntelliDb\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Salehhashemi\LaravelIntelliDb\OpenAi;
use Symfony\Component\Console\Input\InputOption;

class AiMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $name = 'ai:migration';

    /**
     * The console command description.
     */
    protected $description = 'Create a new migration using AI';

    public function __construct(private readonly OpenAi $openAi)
    {
        parent::__construct();
    }

    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputOption::VALUE_REQUIRED, 'The name of the migration')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'The description of the migration')
            ->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'The table name for the migration')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'The location where the migration file should be created');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->getNameInput();
        $description = $this->getDescriptionInput();
        $table = $this->option('table');
        $path = $this->option('path');

        if ($table && is_string($table) && ! Schema::hasTable($table)) {
            $this->error("The table '{$table}' does not exist.");

            return 1;
        }

        $schema = null;
        if ($table && is_string($table)) {
            $schema = Schema::getColumnListing($table);
        }

        $prompt = $this->createAiPrompt($description, $schema);

        $this->info('Generating AI migration, this might take a few moments...');

        try {
            $migrationContent = $this->fetchAiGeneratedContent($prompt);

            if (! is_string($path) && $path !== null) {
                $this->error('Invalid path provided.');

                return 1;
            }

            $this->createMigrationFile($name, $migrationContent, $path);
        } catch (RequestException $e) {
            $this->error('Error fetching AI-generated content: '.$e->getMessage());

            return 1;
        } catch (Exception $e) {
            $this->error('Error occurred: '.$e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * Get the name input from user.
     */
    private function getNameInput(): string
    {
        $name = $this->argument('name');
        if (! $name) {
            $name = $this->ask($this->promptForMissingArgumentsUsing()['name']);
        }

        return Str::snake(trim($name));
    }

    /**
     * Get the description input from user.
     */
    private function getDescriptionInput(): string
    {
        $description = $this->option('description');
        if (! $description) {
            $description = $this->ask('Please describe the migration you want to generate (e.g., "Add email column to users table")');
        }

        return $description;
    }

    /**
     * Create an AI prompt for migration generation.
     *
     * @param  string[]|null  $schema The schema information, if available.
     */
    private function createAiPrompt(string $description, ?array $schema): string
    {
        $prompt = "Generate a Laravel migration file that does the following:\n$description";

        if ($schema) {
            $prompt .= "\nThe current schema of the table is as follows:\n".implode(', ', $schema);
        }

        $prompt .= "\nProvide only the final Laravel migration file code using the anonymous class format like this:";
        $prompt .= "\n<?php\n\nreturn new class extends Migration {\n// migration methods\n};\n";
        $prompt .= "\nInclude everything like php tag and namespace, without any explanations or additional context.";
        $prompt .= "\nInclude type hints for methods and their arguments.";

        return $prompt;
    }

    /**
     * Fetch the AI-generated content.
     *
     * @throws RequestException
     */
    private function fetchAiGeneratedContent(string $prompt): string
    {
        return $this->openAi->execute($prompt, 2000);
    }

    /**
     * Create the migration file.
     */
    private function createMigrationFile(string $name, string $content, ?string $path): void
    {
        $filename = date('Y_m_d_His').'_'.$name.'.php';
        $path = $path ?? database_path('migrations');
        $filepath = $path.'/'.$filename;

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($filepath, $content);

        $this->info(sprintf('Migration [%s] created successfully.', $name));
    }

    /**
     * Prompt for missing arguments.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What should the migration be named?',
        ];
    }
}
