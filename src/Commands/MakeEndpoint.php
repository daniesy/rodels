<?php

namespace Daniesy\Rodels\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEndpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:endpoint {name : The name of the endpoint that will be created.} {--r|rodel : Create a rodel automatically.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make am endpoint';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $endpointName = ucfirst($this->argument('name'));
        $rodelName = Str::singular($endpointName);

        $content = file_get_contents(__DIR__ . "/../Templates/EndpointTemplate.php");
        $content = str_replace("EndpointTemplate", ucfirst($endpointName), $content);
        if (!is_dir(app_path() . "/Endpoints")) {
            mkdir(app_path() . "/Endpoints");
        }

        file_put_contents(app_path() . "/Endpoints/" . $endpointName . ".php", $content);

        if ($this->option('rodel')) {
            $this->call('make:rodel', [
                'name' => $rodelName
            ]);
        }
    }
}
