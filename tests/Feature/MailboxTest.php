<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mailbox;
use Tests\TestCase;

class MailboxTest extends TestCase
{
    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function create_mailbox()
    {
        $this->actingAs($this->user)
            ->postJson(route('mailboxes.store'), [
                'name' => 'Lorem'
            ])
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseHas('mailboxes', [
            'name' => 'Lorem',
        ]);
    }

    /** @test */
    public function update_mailbox()
    {
        $mailbox = Mailbox::factory()->create();

        $this->actingAs($this->user)
            ->putJson(route('mailboxes.update', $mailbox->id), [
                'name' => 'Updated mailbox',
            ])
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseHas('mailboxes', [
            'id' => $mailbox->id,
            'name' => 'Updated mailbox',
        ]);
    }

    /** @test */
    public function show_mailbox()
    {
        $mailbox = Mailbox::factory()->create();

        $this->actingAs($this->user)
            ->getJson(route('mailboxes.show', $mailbox->id))
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'name' => $mailbox->name,
                ]
            ]);
    }

    /** @test */
    public function list_mailbox()
    {
        $mailboxes = Mailbox::factory()->count(2)->create()->map(function ($mailbox) {
            return $mailbox->only(['id', 'name']);
        });

        $this->actingAs($this->user)
            ->getJson(route('mailboxes.index'))
            ->assertSuccessful()
            ->assertJson([
                 'data' => $mailboxes->toArray()
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function delete_mailbox()
    {
        $mailbox = Mailbox::factory()->create([
            'name' => 'Mailbox for delete',
        ]);

        $this->actingAs($this->user)
            ->deleteJson(route('mailboxes.update', $mailbox->id))
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseMissing('mailboxes', [
            'id' => $mailbox->id,
            'name' => 'Mailbox for delete',
        ]);
    }
}
