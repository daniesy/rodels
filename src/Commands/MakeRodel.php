<?php

namespace Daniesy\Rodels\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRodel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:rodel {name : The name of the rodel that will be created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a remote model (rodel)';

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
        $rodelName = $this->argument('name');
        $content = file_get_contents(__DIR__ . "/../Templates/RodelTemplate.php");
        $content = str_replace("RodelTemplate", ucfirst($rodelName), $content);

        if (!is_dir(app_path() . "/Rodels")) {
            mkdir(app_path() . "/Rodels");
        }

        file_put_contents(app_path() . "/Rodels/" . $rodelName . ".php", $content);
    }
}
