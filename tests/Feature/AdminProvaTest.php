<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProvaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_provas_registration_form(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get(route('admin.provas.cadastrar'))
            ->assertOk()
            ->assertSee('type="file"', false);
    }

    public function test_admin_can_upload_a_json_prova_and_see_it_in_the_index(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $response = $this->actingAs($admin)
            ->post(route('admin.provas.store'), [
                'arquivo' => UploadedFile::fake()->createWithContent(
                    'prova.json',
                    json_encode([
                        $this->bloco('Português', 'PROVA-PT-01'),
                        $this->bloco('Informática', 'PROVA-INF-01'),
                    ], JSON_THROW_ON_ERROR)
                ),
            ]);

        $response->assertRedirect(route('admin.provas.index'));
        $this->assertDatabaseCount('questoes', 2);

        $this->get(route('admin.provas.index'))
            ->assertOk()
            ->assertSeeText('Analista Judiciário')
            ->assertSeeText('2');
    }

    private function bloco(string $materia, string $codigo): array
    {
        return [
            'orgao' => 'Tribunal de Teste',
            'banca' => 'Banca Teste',
            'ano' => 2026,
            'cargo' => 'Analista Judiciário',
            'materia' => $materia,
            'tipo_materia' => 'basica',
            'questoes' => [
                [
                    'codigo' => $codigo,
                    'assunto' => 'Conhecimentos gerais',
                    'enunciado' => 'Enunciado de teste',
                    'gabarito' => 'A',
                    'alternativas' => [
                        ['letra' => 'A', 'descricao' => 'Alternativa correta'],
                        ['letra' => 'B', 'descricao' => 'Alternativa incorreta'],
                    ],
                ],
            ],
        ];
    }
}
