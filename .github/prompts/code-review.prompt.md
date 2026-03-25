# Code Review

Faça uma revisão de código das alterações atuais no repositório.

## Instruções

1. Execute `git diff` para ver as mudanças não commitadas (ou `git diff main...HEAD` se já houver commits na branch)
2. Analise cada arquivo alterado verificando:

### Arquitetura DDD
- Domain layer (`app/Domain/`) não importa nada de Infrastructure ou Application
- Actions são `final` e usam injeção de dependência via interfaces de repositório
- DTOs usam `readonly` properties
- Controllers são invokable e fazem apenas: validar → montar DTO → chamar Action → retornar Resource

### Qualidade de Código
- `declare(strict_types=1)` presente em todos os arquivos PHP
- Type hints completos (parâmetros e retorno)
- Imports organizados alfabeticamente, sem imports não utilizados
- Trailing commas em arrays/argumentos multiline
- Sem lógica de negócio em Controllers ou Requests

### Segurança
- Dados sensíveis não expostos em Resources ou logs
- Validação adequada em Form Requests
- Uso correto de middleware `auth:sanctum` em rotas protegidas
- Rate limiting em endpoints públicos

### Frontend (React)
- Componentes funcionais com hooks
- Chamadas à API passam pelo service `api.js` (não axios direto)
- Estado de autenticação via `AuthContext`
- Classes Tailwind sem estilos inline

3. Classifique os problemas encontrados:
   - 🔴 **Crítico** — Bug, falha de segurança, quebra de arquitetura
   - 🟡 **Importante** — Tipo faltando, validação incompleta, teste ausente
   - 🔵 **Sugestão** — Melhoria de legibilidade ou simplificação

4. Se não houver problemas, confirme que o código está pronto para merge
