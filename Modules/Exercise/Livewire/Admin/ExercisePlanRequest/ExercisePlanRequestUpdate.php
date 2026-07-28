<?php

namespace Modules\Exercise\Livewire\Admin\ExercisePlanRequest;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;

#[title('مشاهده برنامه')]
class ExercisePlanRequestUpdate extends Component
{
    public ?ExercisePlanRequest $exercisePlanRequest = null;

    public int $session_count = 1;

    public array $shouldUpdate = [];

    public int $status = ExercisePlanRequestEnum::COMPLETED->value;

    public $start_at = 0;

    public $end_at = 0;

    public $donloadPdf = null;
    public function mount()
    {


        $exercise_plan_request = request()->route('exercise_plan_request');
        if (!($exercise_plan_request instanceof ExercisePlanRequest)) {
            //return error
            abort(404);
        }
        $this->authorize('update', $exercise_plan_request);
        $this->exercisePlanRequest = $exercise_plan_request;
        $this->session_count = $exercise_plan_request->session_count;
        $this->status = $exercise_plan_request->status->value;
        $this->start_at = $exercise_plan_request->start_at?->timestamp ?? Carbon::now()->timestamp;
        $this->end_at = $exercise_plan_request->end_at?->timestamp ?? Carbon::now()->addMonth()->timestamp;
        if ($exercise_plan_request->status->isOr([
            ExercisePlanRequestEnum::COMPLETED,
            ExercisePlanRequestEnum::ENDED,
            ExercisePlanRequestEnum::PENDING,
        ])) {
            foreach ($exercise_plan_request->details as $detail) {
                $this->shouldUpdate[$detail->id] = [
                    'exercise_name' => $detail->exercise_name,
                    'exercise_id' => $detail->exercise_id,
                    'reps' => array_pad($detail->reps, 5, 0),
                ];
            }
        }
        if (!empty($this->exercisePlanRequest->detail) && array_key_exists('pdf_file_address', $this->exercisePlanRequest->detail)) {
            $this->donloadPdf = $this->exercisePlanRequest->detail['pdf_file_address'];
        }
    }

    public function update()
    {
        $this->exercisePlanRequest?->update([
            'status' => ExercisePlanRequestEnum::tryFrom($this->status) ?? ExercisePlanRequestEnum::getDefault(),
            'start_at' => Carbon::createFromTimestamp($this->start_at),
            'end_at' => Carbon::createFromTimestamp($this->end_at),
        ]);

        foreach ($this->shouldUpdate as $id => $detail) {
            //remove 0 from reps
            $this->exercisePlanRequest->details()->find($id)?->update([
                'exercise_name' => $detail['exercise_name'],
                'exercise_id' => $detail['exercise_id'],
                'reps' => array_filter($detail['reps'], fn ($rep) => $rep != '0'),
            ]);
        }
        $this->createPdfFile();

        return redirect()->route('admin.exercise_plan_request.index')->with('success', 'برنامه با موفقیت بروزرسانی شد');
    }
    public function createpdfFile(): void
    {
        //create pdf file
        $exercise_plan_request = $this->exercisePlanRequest ;
        $details = $this->exercisePlanRequest->details()->orderBy('id')->get();
        $mainFile = View::make('exercise::admin.pdf', compact('exercise_plan_request', 'details'));
        $fileName = 'exercise-' . $this->exercisePlanRequest->id . '.html';
        $file_path = '/tmp/pdf/' . $fileName;
        File::put($file_path, $mainFile);
        File::chmod($file_path, 0777);
        //create pdf file
    }

    public function rePlan(): void
    {
        $this->exercisePlanRequest->rePlan();
    }

    public function render()
    {

        $beforeAfterExercise = Exercise::secondary()->get();

        return view('exercise::livewire.admin.exercise-plan-request.exercise-plan-request-update', compact('beforeAfterExercise'));
    }
}
