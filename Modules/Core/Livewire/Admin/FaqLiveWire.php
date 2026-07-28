<?php

namespace Modules\Core\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Core\Entities\Faq;

#[title('سوالات متداول اپلیکیشن')]
class FaqLiveWire extends Component
{
    public String $question, $answer;

    public ?int $faqId = null;


    function changeOrder($from,$to){
        $faqFrom = \Modules\Core\Entities\Faq::find($from);
        $faqTo = \Modules\Core\Entities\Faq::find($to);
        if(!$faqFrom || !$faqTo){
            return;
        }
        $this->authorize('update', $faqFrom);
        $fromShouldBe = $faqTo->priority;
        $toShouldBe = $faqFrom->priority;
        $faqFrom->update([
            'priority' => $fromShouldBe,
        ]);
        $faqTo->update([
            'priority' => $toShouldBe,
        ]);
        $this->dispatch('success-saving', message:'تغییرات با موفقیت ذخیره شد');
    }

    public function edit($id){
        $faq = \Modules\Core\Entities\Faq::find($id);
        $this->authorize('update', $faq);
        $this->faqId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->dispatch('scroll-to-form');

    }
    public function submit(){
        $this->validate([
            'question' => 'required',
            'answer' => 'required',
        ],[
            'question.required' => 'سوال را وارد کنید',
            'answer.required' => 'پاسخ را وارد کنید',
        ]);
        //get last priority
        if($this->faqId){
            $faq = \Modules\Core\Entities\Faq::find($this->faqId);
            $this->authorize('update', $faq);
            $faq->update([
                'question' => $this->question,
                'answer' => $this->answer,
            ]);
            $this->dispatch('success-saving', message:'سوال با موفقیت ویرایش شد');
            $this->faqId = null;
            $this->question = $this->answer = '';
            return;
        }
        $this->authorize('create', Faq::class);
        $lastPriority = \Modules\Core\Entities\Faq::priority()->first();
        \Modules\Core\Entities\Faq::create([
            'question' => $this->question,
            'answer' => $this->answer,
            'status' => 1,
            'priority' => $lastPriority ? $lastPriority->priority + 10 : 10,
        ]);
        $this->dispatch('success-saving', message:'سوال با موفقیت ثبت شد');
        $this->question = $this->answer = '';
    }

    public function toggleStatus($id){
        $faq = \Modules\Core\Entities\Faq::find($id);
        $this->authorize('update', $faq);
        $faq->update([
            'status' => !$faq->status,
        ]);
        $this->dispatch('success-saving', message:'وضعیت با موفقیت تغییر کرد');
    }
    #[On('delete')]
    public function delete(Faq $model)
    {
        $this->authorize('delete', $model);
        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        $this->dispatch('success-saving', message:'سوال با موفقیت حذف شد');
    }
    public function render()
    {
        $faqs = \Modules\Core\Entities\Faq::priority()->get();
        return view('core::livewire.admin.faq', compact('faqs'));
    }
}
