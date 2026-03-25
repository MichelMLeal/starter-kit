# Criar Branch

Crie uma nova branch Git a partir da `main` para a tarefa descrita pelo usuário.

## Instruções

1. Faça `git fetch origin` e certifique-se de estar na `main` atualizada
2. Gere o nome da branch seguindo o padrão: `{tipo}/{slug-descritivo}`
   - Tipos: `feature`, `fix`, `refactor`, `chore`, `hotfix`
   - Slug: kebab-case, máximo 5 palavras, em inglês
   - Exemplo: `feature/user-profile-avatar-upload`
3. Se o usuário fornecer um número de issue/ticket, prefixe o slug: `feature/123-user-profile`
4. Crie a branch e faça checkout
5. Exiba um resumo:
   - Nome da branch criada
   - Tipo da tarefa
   - Breve descrição do que será feito

## Exemplo de uso

> "Preciso criar uma tela de edição de perfil do usuário, issue #42"
> → `feature/42-user-profile-edit`
