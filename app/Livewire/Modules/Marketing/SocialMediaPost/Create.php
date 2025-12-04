<?php

namespace App\Livewire\Modules\Marketing\SocialMediaPost;

use App\Livewire\Forms\Modules\Marketing\SocialMediaPost\Form;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];

    public function save()
    {
        $post = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $post->id,
            'name' => $post->piece_name
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('marketing.socialmedia.index');
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
        ]);
    }
}
