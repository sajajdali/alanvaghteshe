<?php

namespace Modules\Exercise\app\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Symfony\Component\Console\Input\InputArgument;

class createPdfCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:createExercisePdf {id} {uuid}';

    /**
     * The console command description.
     */
    protected $description = 'create an pdf file from exercise.';

    /**
     * Create a new command instance.
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
        $exercise_plan_request = ExercisePlanRequest::find($this->argument('id'));
        $uuid = $this->argument('uuid');
        $link = 'https://pdf.jesmino.com/' . $uuid . '.pdf';
        $newDetail = array_merge($exercise_plan_request->detail ?? [], ['pdf_file_address' => $link,'pdf_uuid' => $uuid]);
        $exercise_plan_request->update([
            'detail' => $newDetail,
        ]);
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['uuid', InputArgument::REQUIRED, 'pdf file uuid .',
            'id', InputArgument::REQUIRED, 'ExercisePlanRequest id.'
        ],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
