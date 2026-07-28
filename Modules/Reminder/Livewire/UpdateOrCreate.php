<?php

namespace Modules\Reminder\Livewire;

use Livewire\Component;
use Modules\Reminder\Enum\ActiveEnum;
use Modules\Reminder\app\Models\Reminder;
use Modules\Reminder\Enum\ReminderSendTypeEnum;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;

class UpdateOrCreate extends Component
{

    public array $fetchData = ['parametrCounter' => 0];
    public array $form = [
        'doctors' => 'all',
        'sendType' => ReminderSendTypeEnum::SMS,
        'sendDate' => 'sameDay',
        'session_number' =>null,
        'active' => 1,
        'timeSend' => 2,
    ];
    public function addMoreParam()
    {
        $this->fetchData['parametrCounter'] = $this->fetchData['parametrCounter'] + 1;
    }
    public function removeParam($i)
    {
        if (isset($this->form['parametr'][$i])) {
            unset($this->form['parametr'][$i]);
            $this->form['parametr'] = array_values($this->form['parametr']);
        }
        $this->fetchData['parametrCounter'] = $this->fetchData['parametrCounter'] - 1;
    }
    public function rules()
    {
        $custumrules = [];
        if (isset($this->form['sendType'])) {
            match ($this->form['sendType']) {
                ReminderSendTypeEnum::SMS => $custumrules['form.smsTemplateName'] = 'required',
                ReminderSendTypeEnum::NOTIFICATION , ReminderSendTypeEnum::WHATSAPP => $custumrules['form.notificationText'] = 'required',
            };
        }
        $generalRuls = [
            'form.send.day'          => 'required',
            'form.sendFor'           => 'required',
        ];
        return array_merge($custumrules, $generalRuls);
    }
    public function messages()
    {
        return [
            'form.specificDoctors.required_if' => 'لطفا پزشک مورد نظر را انتخاب کنید',
            'form.smsTemplateName.required' => 'لطفا نام قالب پیامکی را وارد کنید ',
            'form.notificationText.required' => 'لطفا متن نوتیفیکشن را وارد کنید ',
            'form.send.time.required'        => 'لطفا ساعت ارسال را وارد کنید',
            'form.send.day.required'         => 'لطفا روز ارسال را وارد کنید',
            'form.sendFor.required'         => 'تعین کنید که این یادآور برای چه بخشی ارسال شود',
        ];
    }
    public function storeReminder()
    {

        $this->validate();
        $this->StoreDBReminder();
    }
    private function StoreDBReminder()
    {

        $status = $this->form['sendType'];
        $body = match ($status) {
            ReminderSendTypeEnum::SMS          => $this->form['smsTemplateName'],
            ReminderSendTypeEnum::NOTIFICATION ,ReminderSendTypeEnum::WHATSAPP => $this->form['notificationText'],
        };
        $parameter = isset($this->form['parametr']) ? $this->form['parametr'] : null;
        if (isset($this->form['service']) &&  $this->form['service'] != null) {
            // $service = Service::find($this->form['service']);
        }
        $active = $this->form['active'] == true ? ActiveEnum::ACTIVE : ActiveEnum::DEACTIVE;
        $model = [
            'send_for'     => $this->form['sendFor'],
            'status'       => $status,
            'body'         => $body,
            'parameters'   => $parameter,
            'send_day'     => $this->form['send']['day'],
            'session_number'    => $this->form['session_number'],
            'active'       => $active,
        ];
        if (isset($this->fetchData['reminder'])) {
            // Update the Reminder through the relationship
            $this->fetchData['reminder']->update($model);
            $msg = 'یادآور با موفقیت تغییر کرد' ;
        } else {
            Reminder::create($model);
            $msg = 'یادآور با موفقیت اضافه شد' ;
        }
        return redirect()->route('admin.reminder.list')->with('success', $msg);
    }
    // در Livewire component (UpdateOrCreate.php)
    public function isDietOrExerciseReminder(): bool
    {
        $value = $this->form['sendFor'] ?? null;

        return in_array($value, [
            ReminderTypeEnum::DIET,
            ReminderTypeEnum::EXERCISE,
            ReminderTypeEnum::DIET->value,
            ReminderTypeEnum::EXERCISE->value,
        ]);
    }
    private function fillTheInputs()
    {
        $this->form['sendFor'] = $this->fetchData['reminder']->send_for;
        $this->form['sendType'] = $this->fetchData['reminder']->status;
        $this->form['session_number'] = $this->fetchData['reminder']['session_number'] ?? null;
        $body = match ($this->form['sendType']) {
            ReminderSendTypeEnum::SMS          => $this->form['smsTemplateName']  = $this->fetchData['reminder']->body,
            ReminderSendTypeEnum::NOTIFICATION , ReminderSendTypeEnum::WHATSAPP => $this->form['notificationText'] = $this->fetchData['reminder']->body,
        };
        if (!empty($this->fetchData['reminder']->parameters)) {
            $this->fetchData['parametrCounter'] = count($this->fetchData['reminder']->parameters) - 1;
            $this->form['parametr'] = $this->fetchData['reminder']->parameters;
        }
        $this->form['send']['day']  =  $this->fetchData['reminder']->send_day;
        $this->form['active']    = $this->fetchData['reminder']->active;
    }
    public function mount()
    {
        if (! empty(request()->route('reminder'))) {
            $this->fetchData['reminder'] = Reminder::find(request()->route('reminder'));
            if (!empty($this->fetchData['reminder'])) {
                $this->fillTheInputs();
            }
        }
    }
    public function render()
    {
        return view('reminder::livewire.update-or-create');
    }
}
