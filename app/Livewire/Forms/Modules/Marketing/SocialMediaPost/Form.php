<?php

namespace App\Livewire\Forms\Modules\Marketing\SocialMediaPost;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\SocialMediaPost;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $piece_name = '';

    #[Validate('nullable|string|max:255')]
    public $mediums = '';

    #[Validate('nullable|string|max:255')]
    public $content_type = '';

    #[Validate('nullable|date')]
    public $scheduled_date = '';

    #[Validate('nullable|exists:projects,id')]
    public $project_id = '';

    #[Validate('nullable|exists:users,id')]
    public $responsible_id = '';

    #[Validate('required|exists:tags,id')]
    public $status_id = '';

    #[Validate('nullable|string|max:1000')]
    public $comments = '';

    public function store()
    {
        $this->validate();

        $post = SocialMediaPost::create([
            'piece_name' => $this->piece_name,
            'mediums' => $this->mediums,
            'content_type' => $this->content_type,
            'scheduled_date' => $this->scheduled_date,
            'project_id' => $this->project_id ?: null,
            'responsible_id' => $this->responsible_id ?: null,
            'status_id' => $this->status_id ?: null,
            'comments' => $this->comments,
        ]);

        return $post;
    }

    public function update(SocialMediaPost $post)
    {
        $this->validate();

        $post->update([
            'piece_name' => $this->piece_name,
            'mediums' => $this->mediums,
            'content_type' => $this->content_type,
            'scheduled_date' => $this->scheduled_date,
            'project_id' => $this->project_id ?: null,
            'responsible_id' => $this->responsible_id ?: null,
            'status_id' => $this->status_id ?: null,
            'comments' => $this->comments,
        ]);
    }

    public function setSocialMediaPost(SocialMediaPost $post)
    {
        $this->piece_name = $post->piece_name;
        $this->mediums = $post->mediums;
        $this->content_type = $post->content_type;
        $this->scheduled_date = $post->scheduled_date?->format('Y-m-d');
        $this->project_id = $post->project_id;
        $this->responsible_id = $post->responsible_id;
        $this->status_id = $post->status_id;
        $this->comments = $post->comments;
    }
}
