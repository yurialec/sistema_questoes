<?php

namespace Tests\Feature;

use App\Models\Banca;
use App\Models\DominioBanca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DominioBancaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_banca_domain(): void
    {
        $user = User::factory()->create();
        $banca = Banca::create(['nome' => 'Banca de teste']);
        $dominio = DominioBanca::create([
            'user_id' => $user->id,
            'banca_id' => $banca->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->delete(route('dashboard.dominio-bancas.destroy', $dominio));

        $response
            ->assertRedirect('/dashboard')
            ->assertSessionHas('success', 'Domínio da banca excluído com sucesso!');

        $this->assertDatabaseMissing('dominio_bancas', ['id' => $dominio->id]);
    }

    public function test_user_cannot_delete_another_users_banca_domain(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $banca = Banca::create(['nome' => 'Banca de teste']);
        $dominio = DominioBanca::create([
            'user_id' => $owner->id,
            'banca_id' => $banca->id,
        ]);

        $this
            ->actingAs($user)
            ->delete(route('dashboard.dominio-bancas.destroy', $dominio))
            ->assertForbidden();

        $this->assertDatabaseHas('dominio_bancas', ['id' => $dominio->id]);
    }
}
