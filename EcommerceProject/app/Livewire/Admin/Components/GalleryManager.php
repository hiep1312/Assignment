<?php

namespace App\Livewire\Admin\Components;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\UploadImageRequest;
use App\Repositories\Contracts\ImageableRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class GalleryManager extends Component
{
    use WithFileUploads, AutoValidatesRequest, WithPagination;

    protected ImageRepositoryInterface $repository;
    protected ImageableRepositoryInterface $imageableRepository;
    protected $request = UploadImageRequest::class;

    public $id;
    public $eventName;

    public string $searchImage = '';
    public array $photos = [];
    public array $selectedImageIds = [];
    public ?int $viewingImageId = null;
    public ?array $modalConfirmInfo = null;

    public function mount(
        string $id = 'imagePickerModal',
        string $eventName = 'images.attached'
    ){
        $this->fill(compact('id', 'eventName'));
    }

    public function boot(ImageRepositoryInterface $repository, ImageableRepositoryInterface $imageableRepository){
        $this->repository = $repository;
        $this->imageableRepository = $imageableRepository;
    }

    public function updatedPhotos(){
        $this->saveUploadedPhotos();
    }

    public function saveUploadedPhotos(){
        $this->validate();

        array_walk($this->photos, function(TemporaryUploadedFile $photo){
            $originalName = $photo->getClientOriginalName();
            $originalExtension = $photo->getClientOriginalExtension();
            $baseName = basename($originalName, '.' . $originalExtension);
            $filename = Str::limit($baseName, 80, '') . uniqid() . '.' . $photo->extension();

            array_push(
                $this->selectedImageIds,
                $this->repository->create(['image_url' => storeImage(image: $photo, folder: 'images', filename: $filename)])->id
            );
        });
    }

    public function showUploadError(){
        $this->addError('photos.*', "Oops! Something went wrong while uploading your images.");
    }

    public function deleteImages(?int $id = null){
        $this->repository->delete(
            $id ??
            fn(&$query) => (empty($this->selectedImageIds) ? $query->whereNot('id', -1) : $query->whereIn('id', $this->selectedImageIds)),
            function ($images){
                $this->imageableRepository->delete(
                    fn(&$query) => ($images instanceof Collection)
                        ? $query->whereIn('image_id', $images->pluck('id'))
                        : $query->where('image_id', $images->id)
                );
            }
        );

        $this->reset('selectedImageIds', 'modalConfirmInfo');
    }

    public function dispatchSelectedImages(){
        $this->dispatch($this->eventName, $this->selectedImageIds);
        $this->reset('searchImage', 'photos', 'selectedImageIds', 'viewingImageId', 'modalConfirmInfo');
    }

    public function render()
    {
        $images = $this->repository->getAll(criteria: function(&$query){
            $query->when($this->searchImage, function($innerQuery) {
                $innerQuery->whereLike('image_url', '%'. trim($this->searchImage) .'%');
            });

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'imagePickerPage');

        return view('admin.components.gallery-manager', compact('images'));
    }
}
