<?php

namespace Modules\Reminder\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Modules\Reminder\Enum\ActiveEnum;
use Modules\Reminder\app\Models\Reminder;
use Modules\Reminder\Enum\ReminderStatusEnum;

class ReminderList extends Component
{

    #[Url]
    public $search = [];
    public $searchPanel = '';

    public function startSearch()
    {
        $this->render();
    }

    public function resetProperties()
    {
        $this->search = [];
        $this->searchPanel = '';
        $this->dispatch('closeCollaps', true);
        $this->render();
    }
    #[On('delete')]
    public function delete(Reminder $model)
    {
        $model->delete();
        return redirect()->route('admin.reminder.list')->with('success', 'تخصص با موفقیت حذف شد.');
    }
    public function render()
    {
        $Reminders = Reminder::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', 'LIKE', '%' . $this->search['id'] . '%');
        })->when(isset($this->search['sendtype']), function ($query) {
            return $query->where('status',ReminderStatusEnum::tryFrom($this->search['sendtype']));
        })->when(isset($this->search['status']), function ($query) {
            return $query->where('active',ActiveEnum::tryFrom($this->search['status']));
        })->orderByDesc('id')->paginate(20);
        return view('reminder::livewire.reminder-list', [
            'Reminders' => $Reminders
        ]);
    }

}
