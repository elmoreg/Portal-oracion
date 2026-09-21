<?php

namespace App\Livewire;

use App\Enums\MessageAuthorType;
use App\Models\PrayerRequest;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class PrayerChat extends Component
{
    public PrayerRequest $prayerRequest;

    public MessageAuthorType $viewerRole;

    public ?int $viewerUserId = null;

    public string $body = '';

    public function mount(PrayerRequest $prayerRequest, MessageAuthorType $viewerRole, ?int $viewerUserId = null): void
    {
        $this->prayerRequest = $prayerRequest;
        $this->viewerRole = $viewerRole;
        $this->viewerUserId = $viewerUserId;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $this->prayerRequest->messages()->create([
            'author_type' => $this->viewerRole,
            'user_id' => $this->viewerUserId,
            'body' => $this->body,
        ]);

        $this->body = '';
    }

    #[On('prayer-request-updated')]
    public function refreshMessages(): void
    {
        $this->prayerRequest->refresh();
    }

    public function getMessagesProperty(): Collection
    {
        return $this->prayerRequest->messages()->with('user')->get();
    }

    public function render()
    {
        return view('livewire.prayer-chat', [
            'messages' => $this->messages,
        ]);
    }
}
