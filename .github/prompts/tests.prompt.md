# Gerar Testes

Crie testes para o código indicado pelo usuário seguindo os padrões do projeto.

## Instruções

1. Identifique o que precisa ser testado (Action, Controller/endpoint, Model, etc.)
2. Determine o tipo de teste:
   - **Feature test** → endpoints HTTP, fluxos completos (controller → action → DB)
   - **Unit test** → lógica isolada de Actions, DTOs, validações de domínio

3. Crie os testes seguindo os padrões do projeto:

### Estrutura Pest
```php
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does something specific', function () {
    // Arrange — preparar dados com factories
    $user = User::factory()->create();

    // Act — executar a ação
    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Assert — verificar resultado
    $response->assertOk()
        ->assertJsonStructure(['access_token', 'refresh_token']);
});
```

### Convenções
- Arquivo em `tests/Feature/{Dominio}/` ou `tests/Unit/{Dominio}/`
- Nome do arquivo: `{Funcionalidade}Test.php`
- Descrição do teste em inglês, começando com verbo: `it('creates a new user')`
- Usar `RefreshDatabase` em todo teste que toca no banco
- Factories para criar dados de teste (nunca inserir manualmente)
- `postJson()`/`getJson()` para endpoints (não `post()`/`get()`)
- Testar os cenários: sucesso, validação, não autorizado, não encontrado

4. Cubra estes cenários obrigatórios:
   - ✅ Happy path (fluxo esperado)
   - ❌ Validação (campos obrigatórios, formatos inválidos)
   - 🔒 Autenticação (401 sem token, 403 sem permissão)
   - 🔄 Edge cases (duplicatas, limites, dados vazios)

5. Execute `./vendor/bin/pest --filter={TestFile}` para validar que os testes passam
