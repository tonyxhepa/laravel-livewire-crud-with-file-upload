<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostIndex extends Component
{
    use WithFileUploads;

    public $showingPostModal = false;

    public $title;
    public $newImage;
    public $body;
    public $oldImage;
    public $isEditMode = false;
    public $post;

    public function showPostModal()
    {
        $this->reset();
        $this->showingPostModal = true;
    }


    public function storePost()
    {
        $this->validate([
            'newImage' => 'image|max:1024', // 1MB Max
            'title' => 'required',
            'body' => 'required'
        ]);

        $image = $this->newImage->store('public/posts');

        Post::create([
            'title' => $this->title,
            'image' => $image,
            'body' => $this->body,
        ]);
        $this->reset();
    }

    public function showEditPostModal($id)
    {
        $this->post = Post::findOrFail($id);
        $this->title = $this->post->title;
        $this->body = $this->post->body;
        $this->oldImage = $this->post->image;
        $this->isEditMode = true;
        $this->showingPostModal = true;
    }

    public function updatePost()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $image = $this->post->image;
        if ($this->newImage) {
            $image = $this->newImage->store('public/posts');
        }

        $this->post->update([
            'title' => $this->title,
            'image' => $image,
            'body' => $this->body
        ]);
        $this->reset();
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        Storage::delete($post->image);
        $post->delete();
        $this->reset();
    }

    public function render()
    {
        return view('livewire.post-index', [
            'posts' => Post::all()
        ]);
    }
}
