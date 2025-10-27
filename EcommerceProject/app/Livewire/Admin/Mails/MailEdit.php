<?php

namespace App\Livewire\Admin\Mails;

use App\Helpers\AutoValidatesRequest;
use App\Helpers\MailTemplateHelper;
use App\Http\Requests\MailRequest;
use App\Repositories\Contracts\MailRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class MailEdit extends Component
{
    use AutoValidatesRequest;

    public $id;
    public ?string $subject = null;
    public string $body = '';
    public int $type = 0;

    protected MailRepositoryInterface $repository;
    protected $request = MailRequest::class;

    public function boot(MailRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $mail){
        $mail = $this->repository->find(idOrCriteria: $mail, throwNotFound: true);

        $this->fill($mail->only([
            'id',
            'subject',
            'body',
            'type',
        ]));
    }

    public function updatedBody(){
        $this->body = preg_replace('/\{\{\s*([^\}\s]*)\s*\}\}/', '{{${1}}}', $this->body);
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            $this->id,
            $this->only([
                'subject',
                'body',
                'type',
            ]) + [
                'variable' => MailTemplateHelper::getUsedPlaceholders($this->body, $this->type),
            ]
        );

        return redirect()->route('admin.mails.index')->with('data-changed', ['Mail has been updated successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('subject', 'body', 'type');
        $this->mount($this->id);
    }

    #[Title('Edit Mail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.mails.mail-edit');
    }
}
