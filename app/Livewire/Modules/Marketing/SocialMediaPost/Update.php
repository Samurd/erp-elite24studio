<?php

namespace App\Livewire\Modules\Marketing\SocialMediaPost;

use App\Livewire\Forms\Modules\Marketing\SocialMediaPost\Form;
use App\Models\SocialMediaPost;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public SocialMediaPost $post;

    public function mount(SocialMediaPost $post)
    {
        $this->post = $post;
        $this->form->setSocialMediaPost($post);
    }

    public function save()
    {
        $this->form->update($this->post);
        
        return redirect()->route('marketing.socialmedia.index')
            ->with('success', 'Publicación actualizada exitosamente.');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_publicacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener proyectos
        $projects = Project::orderBy('name')->get();
        
        // Obtener usuarios para el selector de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.marketing.social-media-post.create', [
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
            'post' => $this->post,
        ]);
    }
}
