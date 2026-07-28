<?php

namespace Modules\Diet\app\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\DietRequestStatusEnum;

class MakeRejimJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dietRequest;
    public $actionRequest;

    /**
     * @param $dietRequest
     * @param $actionRequest
     */
    public function __construct($dietRequest, $actionRequest)
    {
        $this->dietRequest = $dietRequest;
        $this->actionRequest = $actionRequest;
    }
    /**
     * Create a new job instance.
     */


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $resultMakeDiet = app('dietService')->makeDietFromBasicFoods($this->dietRequest, $this->actionRequest);
        $detail = $this->dietRequest->detail;
        $detail[DietRequest::KEY_DETAIL_REPORT_MAKE_DIET] = app('dietService')->roundArrayNumbers($resultMakeDiet);
        $condition = [
            'detail' => $detail
        ];
        // insert fat
        $result = app('dietService')->resultFilterAndDisplayErrors($resultMakeDiet);
        if ($result['count'] == 0) {
            $condition['start_date']  = Carbon::now()->addDay()->toDateString();
            $condition['end_date']    = Carbon::now()->addDays($this->dietRequest->dietPlan->day_count + 1)->toDateString();
            $condition['status']      = DietRequestStatusEnum::ACTIVE;
            $condition['active']      = true;
            CreateJsonDietJob::dispatch($this->dietRequest)->onQueue('low'); // job regenerate json file
        } else {
            $condition['status'] = DietRequestStatusEnum::REJECTED;
            $condition['active'] = false;
        }
        $this->dietRequest->update($condition);
    }
}
