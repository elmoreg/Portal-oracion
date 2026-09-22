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

    public ?string $viewerName = null;

    public string $authorName = '';

    public string $body = '';

    public function mount(PrayerRequest $prayerRequest, MessageAuthorType $viewerRole, ?int $viewerUserId = null, ?string $viewerName = null): void
    {
        $this->prayerRequest = $prayerRequest;
        $this->viewerRole = $viewerRole;
        $this->viewerUserId = $viewerUserId;
        $this->viewerName = $viewerName;

        // Quien comenta con una cuenta usa el nombre de su cuenta. Quien comenta
        // de forma anónima (la persona que hizo la petición) tiene que escribir
        // un nombre; si ya dio uno al crear la petición, lo dejamos precargado.
        $this->authorName = $this->isAuthenticated()
            ? (string) $this->viewerName
            : (string) $prayerRequest->requester_name;
    }

    public function isAuthenticated(): bool
    {
        return $this->viewerUserId !== null;
    }

    public function sendMessage(): void
    {
        $rules = [
            'body' => ['required', 'string', 'max:2000'],
        ];

        if (! $this->isAuthenticated()) {
            $rules['authorName'] = ['required', 'string', 'max:120'];
        }

        $this->validate($rules);

        $authorName = $this->isAuthenticated()
            ? (string) $this->viewerName
            : trim($this->authorName);

        $this->prayerRequest->messages()->create([
            'author_type' => $this->viewerRole,
            'user_id' => $this->viewerUserId,
            'author_name' => $authorName,
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
