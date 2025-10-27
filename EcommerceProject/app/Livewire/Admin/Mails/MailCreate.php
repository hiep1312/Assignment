<?php

namespace App\Livewire\Admin\Mails;

use App\Helpers\AutoValidatesRequest;
use App\Helpers\MailTemplateHelper;
use App\Http\Requests\MailRequest;
use App\Repositories\Contracts\MailRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class MailCreate extends Component
{
    use AutoValidatesRequest;

    public ?string $subject = null;
    public string $body = '';
    public int $type = 0;

    protected MailRepositoryInterface $repository;
    protected $request = MailRequest::class;

    public function boot(MailRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function updatedBody(){
        $this->body = preg_replace('/\{\{\s*([^\}\s]*)\s*\}\}/', '{{${1}}}', $this->body);
    }

    public function store(){
        $this->validate();

        $this->repository->create(
            $this->only([
                'subject',
                'body',
                'type',
            ]) + [
                'variable' => MailTemplateHelper::getUsedPlaceholders($this->body, $this->type),
            ]
        );

        return redirect()->route('admin.mails.index')->with('data-changed', ['New mail template has been created successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('subject', 'body', 'type');
    }

    #[Title('Add New Mail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.mails.mail-create');
    }
}
