<?php

namespace App\Livewire\Admin\Images;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\UploadImageRequest;
use App\Repositories\Contracts\ImageableRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ImageIndex extends Component
{
    use WithPagination, WithFileUploads, AutoValidatesRequest;

    public bool $isTrashed = false;
    protected ImageRepositoryInterface $repository;
    protected ImageableRepositoryInterface $imageableRepository;
    protected $request = UploadImageRequest::class;

    public string $search = '';
    public ?string $datetimeFrom = null;
    public ?string $datetimeTo = null;
    public array $selectedRecordIds = [];
    public array $photos = [];
    public ?int $viewingImageId = null;

    public $singlePhoto = null;
    public ?int $updatingImageId = null;

    public function boot(ImageRepositoryInterface $repository, ImageableRepositoryInterface $imageableRepository){
        $this->repository = $repository;
        $this->imageableRepository = $imageableRepository;
    }

    public function updatedPhotos(){
        $this->saveUploadedPhotos();
    }

    public function updatedSinglePhoto(){
        if(
            is_int($this->updatingImageId) &&
            ($image = $this->repository->find($this->updatingImageId)) &&
            $this->singlePhoto instanceof TemporaryUploadedFile
        ){
            $this->validate([
                'singlePhoto' => 'image|max:10240'
            ], [
                'singlePhoto.image' => 'The file must be an image.',
                'singlePhoto.max' => 'The image must not exceed 10MB.',
            ]);

            Storage::disk('public')->putFileAs('images', $this->singlePhoto, basename($image->image_url));
            $image->touch();

            session()->flash('image-updated-success', ["The image has been successfully updated.", now()->toISOString()]);
        }else{
            session()->flash('image-updated-fail', ["Failed to update the image. Please try again.", now()->toISOString()]);
        }

        $this->reset('singlePhoto', 'updatingImageId');
    }

    public function saveUploadedPhotos(){
        $this->validate();

        array_walk($this->photos, function(TemporaryUploadedFile $photo){
            $originalName = $photo->getClientOriginalName();
            $originalExtension = $photo->getClientOriginalExtension();
            $baseName = basename($originalName, '.' . $originalExtension);
            $filename = Str::limit($baseName, 80, '') . uniqid() . '.' . $photo->extension();

            $this->repository->create(['image_url' => storeImage(image: $photo, folder: 'images', filename: $filename)]);
        });
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'datetimeFrom', 'datetimeTo');
        $this->resetPage();
    }

    #[On('image.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete(
            $id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds),
            function ($images){
                $this->imageableRepository->delete(
                    fn(&$query) => ($images instanceof Collection)
                        ? $query->whereIn('image_id', $images->pluck('id'))
                        : $query->where('image_id', $images->id)
                );
            }
        );
    }

    #[On('image.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('image.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('Image List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $images = $this->repository->getAll(criteria: function(&$query) {
            if($this->isTrashed) $query->onlyTrashed();

            $query->when($this->search, function($innerQuery){
                $innerQuery->whereLike('image_url', '%'. trim($this->search) .'%');
            })->when(
                $this->datetimeFrom,
                fn($innerQuery) => $innerQuery->where('created_at', '>=', $this->datetimeFrom)
            )->when(
                $this->datetimeTo,
                fn($innerQuery) => $innerQuery->where('created_at', '<=', $this->datetimeTo)
            );

            $query->latest('updated_at');
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.images.image-index', compact('images'));
    }
}
