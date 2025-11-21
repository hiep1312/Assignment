<?php

namespace App\Livewire\Admin\Banners;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\BannerRequest;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class BannerEdit extends Component
{
    use AutoValidatesRequest;

    public $id;
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

    public function mount(int $banner){
        $banner = $this->repository->first(criteria: function($query) use ($banner){
            $query->with('imageable');

            $query->where('id', $banner);
        }, throwNotFound: true);

        $this->fill(
            $banner->only([
                'id',
                'title',
                'link_url',
                'status',
            ]) + [
                'image_id' => $banner->imageable?->image_id,
                'position' => $banner->imageable?->position
            ]
        );
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'title',
                'link_url',
                'status',
            ]),
            updatedModel: $bannerUpdated
        );

        $imageableData = $this->only('image_id', 'position');
        if(!$bannerUpdated->imageable){
            $bannerUpdated->imageable()->create($imageableData);
        }else{
            $bannerUpdated->imageable()->update($imageableData);
        }

        return redirect()->route('admin.banners.index')->with('data-changed', ['Banner has been updated successfully.', now()->toISOString()]);
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $this->image_id = $imageIds[0];
    }

    public function resetForm(){
        $this->reset('title', 'link_url', 'status', 'image_id', 'position');
        $this->mount($this->id);
    }

    #[Title('Edit Banner - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $usedPositions = $this->repository->getUsedPositions($this->id);
        $image = is_int($this->image_id) ? $this->imageRepository->find($this->image_id) : null;

        return view('admin.pages.banners.banner-edit', compact('usedPositions', 'image'));
    }
}
