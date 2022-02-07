<?php

namespace App\Http\Livewire;

use App\Models\Partner;
use App\Models\Comment;
use Debugbar;
use Livewire\Component;

class CommentsOverview extends Component
{
    public $partnerId;
    public $editData;
    public $showForm = false;

    protected $rules = [
        'editData.comment' => 'required',
    ];

    public function mount($id){
        $this->partnerId = $id;

    }

    public function render()
    {
        $comments = Comment::where('partner_id','=',$this->partnerId)->orderByDesc('created_at')->paginate();
        $partner = Partner::find($this->partnerId)->load('tasks');
        return view('livewire.comments-overview',compact(['comments','partner']));
    }

    public function showForm(){
        $this->showForm = true;
    }

    public function closeForm(){
        $this->showForm = false;
    }
    public function resetForm(){
        $this->editData =['comment' =>''];
    }

    public function addNewComment(){
        $this->resetForm();
        $this->showForm();
    }


    public function saveComment(){
        $this->validate();

        $comment = new Comment();
        $comment->partner_id = $this->partnerId;
        $comment->fill($this->editData);
        $comment->save();

        $this->showToast('Spremljeno','Posao Spremljen','success');

        $this->closeForm();

    }

}
