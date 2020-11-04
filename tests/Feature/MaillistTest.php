<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Maillist;
use Tests\TestCase;

class MaillistTest extends TestCase
{
    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function create_maillist()
    {
        $this->actingAs($this->user)
            ->postJson(route('maillists.store'), [
                'name' => 'Lorem'
            ])
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseHas('maillists', [
            'name' => 'Lorem',
        ]);
    }

    /** @test */
    public function update_maillist()
    {
        $maillist = Maillist::factory()->create();

        $this->actingAs($this->user)
            ->putJson(route('maillists.update', $maillist->id), [
                'name' => 'Updated maillist',
            ])
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseHas('maillists', [
            'id' => $maillist->id,
            'name' => 'Updated maillist',
        ]);
    }

    /** @test */
    public function show_maillist()
    {
        $maillist = Maillist::factory()->create();

        $this->actingAs($this->user)
            ->getJson(route('maillists.show', $maillist->id))
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'name' => $maillist->name,
                ]
            ]);
    }

    /** @test */
    public function list_maillist()
    {
        $maillists = Maillist::factory()->count(2)->create()->map(function ($maillist) {
            return $maillist->only(['id', 'name']);
        });

        $this->actingAs($this->user)
            ->getJson(route('maillists.index'))
            ->assertSuccessful()
            ->assertJson([
                 'data' => $maillists->toArray()
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
    public function delete_maillist()
    {
        $maillist = Maillist::factory()->create([
            'name' => 'Maillist for delete',
        ]);

        $this->actingAs($this->user)
            ->deleteJson(route('maillists.update', $maillist->id))
            ->assertSuccessful()
            ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

        $this->assertDatabaseMissing('maillists', [
            'id' => $maillist->id,
            'name' => 'Maillist for delete',
        ]);
    }
}
