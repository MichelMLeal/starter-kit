# Migration Review

Analise as migrations pendentes ou recém-criadas para garantir segurança e qualidade.

## Instruções

1. Identifique as migrations a revisar:
   - Se houver mudanças em `database/migrations/`, analise os arquivos alterados
   - Caso contrário, pergunte ao usuário qual migration revisar

2. Para cada migration, verifique:

### Estrutura
- Existe método `down()` funcional que reverte completamente o `up()`
- Tipos de coluna adequados (não usar `string` para o que deveria ser `text`, `integer` para IDs, etc.)
- `timestamps()` presente em tabelas novas
- Foreign keys com `constrained()->cascadeOnDelete()` ou ação explícita de delete

### Performance
- Índices em colunas usadas em `WHERE`, `JOIN`, `ORDER BY`
- Índices compostos quando há queries frequentes com múltiplas colunas
- Colunas `unique` onde necessário para integridade
- Avaliar se a migration pode travar a tabela em produção (tabelas grandes)

### Segurança e Dados
- Não há valores default que exponham dados sensíveis
- Colunas nullable apenas quando faz sentido no domínio
- Migrações destrutivas (drop column/table) estão em migration separada

### Consistência
- Nomenclatura segue convenção Laravel (snake_case, plural para tabelas)
- Segue o padrão das migrations existentes no projeto

3. Se a migration precisa de um Model, verifique se:
   - O Model foi criado em `app/Domain/{Dominio}/Models/`
   - As relações estão definidas nos dois lados
   - `$fillable`, `$casts`, `$hidden` estão configurados

4. Resuma com ✅ aprovado ou liste as correções necessárias
