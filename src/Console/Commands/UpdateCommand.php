<?php

namespace NormanHuth\PimcoreIconSheet\Console\Commands;

use Illuminate\Console\Command;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\SourceException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update icon sheet';

    /**
     * Execute the console command.
     *
     * @throws SourceException
     * @return int
     */
    public function handle(): int
    {
        $this->createCSSJson();
        $this->handleSets();

        return SymfonyCommand::SUCCESS;
    }

    protected function handleSets(): void
    {
        $sets = [];
        $directories = glob(base_path('docs/assets/pimcoreadmin/*'), GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $set = basename($directory);
            $sets[] = $set;
            $files = $this->handleSet($set);

            file_put_contents(base_path('docs/data/sets/'.$set.'.json'), jsonPrettyEncode($files));
        }

        file_put_contents(base_path('docs/data/sets.json'), jsonPrettyEncode($sets));
    }

    protected function handleSet(string $path): array
    {
        $data = [];

        $files = glob(base_path('docs/assets/pimcoreadmin/'.$path.'/*'));
        foreach ($files as $file) {
            $basename = basename($file);
            if (is_dir($file)) {
                $data = array_merge($data, $this->handleSet($path.'/'.$basename));
                continue;
            }
            if ($basename != '_unknown.svg') {
                $data[] = $path.'/'.$basename;
            }
        }

        return $data;
    }

    /**
     * @throws SourceException
     */
    protected function createCSSJson(): void
    {
        $string = file_get_contents(base_path('resources/pimcore/icons.css'));
        $parser = new Parser($string);
        $cssDocument = $parser->parse();

        $contents = [];

        foreach ($cssDocument->getAllDeclarationBlocks() as $block) {
            $class = $value = null;
            foreach ($block->getSelectors() as $selector) {
                $class = $selector->getSelector();
                $class = substr($class, 1);
                $class = explode(':', $class)[0];
            }
            foreach ($block->getRules() as $rule) {
                if ($rule->getRule() == 'background') {
                    $value = $rule->getValue();
                    $value = preg_match('/"([^"]+)"/', $value, $match);
                    if (!empty($match[1])) {
                        $value = str_replace('/bundles/pimcoreadmin/img/', '', $match[1]);
                    }
                }
            }

            if ($value) {
                $contents[$value] = $class;
            }
        }

        file_put_contents(base_path('docs/data/css.json'), jsonPrettyEncode($contents));
    }
}
