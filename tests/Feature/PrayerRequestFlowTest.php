<?php

namespace Tests\Feature;

use App\Enums\MessageAuthorType;
use App\Enums\PrayerRequestStatus;
use App\Enums\UserRole;
use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Volt\Volt;
use Tests\TestCase;

class PrayerRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_submit_an_anonymous_prayer_request_and_gets_a_unique_token(): void
    {
        $response = Volt::test('pages.public.new-request')
            ->set('requester_name', 'María')
            ->set('content', 'Por favor oren por mi salud.')
            ->call('submit');

        $response->assertRedirect();
        $response->assertSessionHas('petition_created', true);

        $this->assertSame(1, PrayerRequest::count());

        $prayerRequest = PrayerRequest::first();

        $this->assertSame('María', $prayerRequest->requester_name);
        $this->assertSame(PrayerRequestStatus::Pending, $prayerRequest->status);
        $this->assertNotEmpty($prayerRequest->public_token);
        $this->assertSame(40, strlen($prayerRequest->public_token));

        // When visiting the show page with the session flash, the encouragement card is shown
        $showResponse = $this->withSession(['petition_created' => true, 'locale' => 'es'])
            ->get(route('prayer.show', $prayerRequest));

        $showResponse->assertSee('¡Tu petición ha sido recibida con amor y fe!');
        $showResponse->assertSee('No estás solo ni sola en este momento');
        $showResponse->assertSee('FILIPENSES 4:6-7');
    }

    public function test_encouragement_message_is_localized(): void
    {
        Http::fake(['https://api.mymemory.translated.net/*' => Http::response([], 200)]);

        $prayerRequest = PrayerRequest::factory()->create();

        // English
        $responseEn = $this->withSession(['petition_created' => true, 'locale' => 'en'])
            ->get(route('prayer.show', $prayerRequest));

        $responseEn->assertSee('Your request has been received with love and faith!');
        $responseEn->assertSee('You are not alone right now');
        $responseEn->assertSee('PHILIPPIANS 4:6-7');

        // Portuguese
        $responsePt = $this->withSession(['petition_created' => true, 'locale' => 'pt'])
            ->get(route('prayer.show', $prayerRequest));

        $responsePt->assertSee('Seu pedido foi recebido com amor e fé!');
        $responsePt->assertSee('Você não está sozinho(a) neste momento');
    }

    public function test_public_tokens_are_unique(): void
    {
        PrayerRequest::factory()->count(20)->create();

        $tokens = PrayerRequest::pluck('public_token');

        $this->assertSame($tokens->count(), $tokens->unique()->count());
    }

    public function test_the_public_show_page_is_reachable_only_with_the_correct_token(): void
    {
        $prayerRequest = PrayerRequest::factory()->create();

        $this->get(route('prayer.show', $prayerRequest))->assertOk();
        $this->get('/p/un-token-que-no-existe')->assertNotFound();
    }

    public function test_the_requester_can_mark_their_own_request_as_answered(): void
    {
        $prayerRequest = PrayerRequest::factory()->create();

        Volt::test('pages.public.show', ['prayerRequest' => $prayerRequest])
            ->set('answer_note', 'Dios respondió')
            ->call('markAnswered');

        $prayerRequest->refresh();

        $this->assertTrue($prayerRequest->is_answered);
        $this->assertSame(PrayerRequestStatus::Answered, $prayerRequest->status);
        $this->assertSame('Dios respondió', $prayerRequest->answer_note);
    }

    public function test_admin_can_assign_an_intercessor_and_status_moves_to_assigned(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $prayerRequest = PrayerRequest::factory()->create();

        $this->actingAs($admin);

        Volt::test('pages.admin.prayer-requests.show', ['prayerRequest' => $prayerRequest])
            ->set('selectedIntercessors', [$intercessor->id])
            ->call('saveAssignments');

        $prayerRequest->refresh();

        $this->assertTrue($prayerRequest->isAssignedTo($intercessor));
        $this->assertSame(PrayerRequestStatus::Assigned, $prayerRequest->status);
    }

    public function test_intercessor_dashboard_only_shows_requests_assigned_to_that_intercessor(): void
    {
        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $otherIntercessor = User::factory()->create(['role' => UserRole::Intercessor]);

        $mine = PrayerRequest::factory()->create();
        $mine->intercessors()->attach($intercessor->id, ['assigned_at' => now()]);

        $notMine = PrayerRequest::factory()->create();
        $notMine->intercessors()->attach($otherIntercessor->id, ['assigned_at' => now()]);

        $this->actingAs($intercessor);

        Volt::test('pages.intercessor.dashboard')
            ->assertSee($mine->content)
            ->assertDontSee($notMine->content);
    }

    public function test_intercessor_cannot_open_a_request_that_is_not_assigned_to_them(): void
    {
        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $prayerRequest = PrayerRequest::factory()->create();

        $this->actingAs($intercessor);

        $this->get(route('intercessor.prayer-requests.show', $prayerRequest))
            ->assertForbidden();
    }

    public function test_guests_and_intercessors_cannot_access_the_admin_area(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $this->actingAs($intercessor)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_chat_messages_are_scoped_to_their_prayer_request(): void
    {
        $prayerRequestA = PrayerRequest::factory()->create();
        $prayerRequestB = PrayerRequest::factory()->create();

        Volt::test('pages.public.show', ['prayerRequest' => $prayerRequestA])
            ->assertSet('prayerRequest.id', $prayerRequestA->id);

        $prayerRequestA->messages()->create([
            'author_type' => MessageAuthorType::Requester,
            'body' => 'Mensaje de A',
        ]);

        $this->assertSame(1, $prayerRequestA->messages()->count());
        $this->assertSame(0, $prayerRequestB->messages()->count());
    }
}
