<?php

namespace App\Livewire\Admin\Mails;

use App\Repositories\Contracts\MailUserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class MailBatches extends Component
{
    use WithPagination;

    protected MailUserRepositoryInterface $repository;

    public string $search = '';
    public ?int $type = null;
    public ?int $status = null;
    public array $selectedRecordIds = [];

    public function boot(MailUserRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function resetFilters(){
        $this->reset('search', 'type', 'status');
        $this->resetPage();
    }

    #[On('mailBatch.deleted')]
    public function delete(?string $batchKey = null){
        $this->repository->delete(function($query) use ($batchKey) {
            is_string($batchKey)
                ? $query->where('batch_key', $batchKey)
                : $query->whereIn('batch_key', $this->selectedRecordIds);
        });
    }

    #[Title('Mail Batches - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $mailBatches = $this->repository->getAllMailBatches(criteria: function(&$query) {
            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('mu.batch_key', '%'. trim($this->search) .'%')
                        ->orwhereLike('m.subject', '%'. trim($this->search) .'%')
                        ->orWhereRaw("CONCAT(u.first_name, ' ', u.last_name) LIKE ?", ['%'. trim($this->search) .'%'])
                        ->orWhereLike('u.email', '%'. trim($this->search) .'%')
                        ->orWhereLike('mu.error_message', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->type !== null,
                fn($innerQuery) => $innerQuery->where('m.type', $this->type)
            )
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('mu.status', $this->status)
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.mails.mail-batches', compact('mailBatches'));
    }
}
