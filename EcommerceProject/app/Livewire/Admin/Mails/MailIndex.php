<?php

namespace App\Livewire\Admin\Mails;

use App\Repositories\Contracts\MailRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class MailIndex extends Component
{
    use WithPagination;

    protected MailRepositoryInterface $repository;

    public string $search = '';
    public ?int $type = null;
    public ?int $hasSubject = null;
    public array $selectedRecordIds = [];

    public function boot(MailRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function resetFilters(){
        $this->reset('search', 'type', 'hasSubject');
        $this->resetPage();
    }

    #[On('mail.deleted')]
    public function delete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[Title('Mail Template List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $mails = $this->repository->getAll(criteria: function(&$query) {
            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('subject', '%'. trim($this->search) .'%')
                        ->orWhereLike('body', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->type !== null,
                fn($innerQuery) => $innerQuery->where('type', $this->type)
            )
            ->when(
                $this->hasSubject !== null,
                fn($innerQuery) => $innerQuery->where('subject', $this->hasSubject ? '!=' : '=', null)
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.mails.mail-index', compact('mails'));
    }
}
