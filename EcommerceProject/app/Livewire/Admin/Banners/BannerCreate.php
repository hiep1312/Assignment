<?php

namespace App\Livewire\Admin\Banners;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\BannerRequest;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class BannerCreate extends Component
{
    use WithFileUploads, AutoValidatesRequest;

    public $title = null;
    public $link_url = '';
    public $status = 1;
    public $image_id = null;
    public $position = null;

    protected BannerRepositoryInterface $repository;
    protected ImageRepositoryInterface $imageRepository;
    protected $request = BannerRequest::class;

    public function boot(BannerRepositoryInterface $repository, ImageRepositoryInterface $imageRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
    }

    public function store(){
        $this->validate();

        $bannerCreated = $this->repository->create($this->only([
            'title',
            'link_url',
            'status',
        ]));

        $bannerCreated->imageable()->create(['image_id' => $this->image_id, 'position' => $this->position]);

        return redirect()->route('admin.banners.index')->with('data-changed', ['New banner has been created successfully.', now()->toISOString()]);
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $this->image_id = $imageIds[0];
    }

    public function resetForm(){
        $this->reset('title', 'link_url', 'status', 'image_id', 'position');
    }

    #[Title('Add New Banner - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $usedPositions = $this->repository->getUsedPositions();
        $image = is_int($this->image_id) ? $this->imageRepository->find($this->image_id) : null;

        return view('admin.pages.banners.banner-create', compact('usedPositions', 'image'));
    }
}
