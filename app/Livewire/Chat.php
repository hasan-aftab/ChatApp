<?php

namespace App\Livewire;

use App\Events\SendMessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Chat extends Component
{
    public $user;
    public $oneTimeMessage;
    public $users = [];
    public $messages = [];

    public function mount() {
//        $this->user = auth()->user();
        $this->users = User::where('id', '!=', auth()->user()->id)->get();
    }

    public function selectUser($selectedUser) {
        $this->user = User::find($selectedUser['id']);
        $this->messages = Message::where(function ($q) use ($selectedUser) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $selectedUser['id']);
        })->orWhere(function ($q) use ($selectedUser) {
            $q->where('sender_id', $selectedUser['id'])->where('receiver_id', auth()->id());
        })->get();
    }

    public function sendMessage() {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->user->id,
            'message' => $this->oneTimeMessage
        ]);
        \broadcast(new SendMessageEvent($message))->toOthers();
        $this->messages[] = $message;
    }


    #[On('echo-private:send-message,SendMessageEvent')]
    public function listenMessage($event) {
        $this->messages[] = Message::find($event['message']['id']);
    }

    public function render() {
        return view('livewire.chat')->layout('layouts.app');
    }
}
