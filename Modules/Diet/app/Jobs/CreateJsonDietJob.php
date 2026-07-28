<?php

namespace Modules\Diet\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Modules\Diet\Entities\DietRequest;

class CreateJsonDietJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public DietRequest $dietRequest;

    /**
     * Create a new job instance.
     */
    public function __construct($dietRequest)
    {
        $this->dietRequest = $dietRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        makeJsonFile($this->dietRequest);
//        $result = app('dietService')->makeDietJsonFile($this->dietRequest);
//        app('dietService')->createDietJsonFile($this->dietRequest , $result);
//
//        //create pdf
//        $user  = $this->dietRequest->user;
//        $dietDetail = $result ;
//        $dietRequest = $this->dietRequest ;
//        $mainFile = View::make('diet::exportDiet', compact('dietDetail', 'user', 'dietRequest'))->render();
//        $fileName = 'diet-' . $this->dietRequest->id . '.html';
//        $directory = '/tmp/pdf/';
//        $filePath = $directory . $fileName;
//
//        // Ensure the directory exists
//        File::ensureDirectoryExists($directory, 0777, true);
//
//        // Save the file and set permissions
//        File::put($filePath, $mainFile);
//        File::chmod($filePath, 0777);
    }

}
