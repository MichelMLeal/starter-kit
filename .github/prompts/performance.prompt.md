# Análise de Desempenho

Analise o desempenho do código indicado pelo usuário, identificando gargalos e propondo melhorias.

## Instruções

1. Identifique o escopo da análise (endpoint, query, página, fluxo completo)

2. Analise as seguintes camadas:

### Queries e Banco de Dados
- Execute `php artisan model:show {Model}` para ver a estrutura
- Identifique N+1 queries (relações carregadas em loop sem `with()`)
- Verifique se há `select *` onde um `select()` específico bastaria
- Procure queries sem índice — cruze com as migrations
- Avalie se há queries que poderiam usar cache
- Verifique uso de `chunk()` ou `cursor()` para grandes volumes

### Eloquent e PHP
- Coleções grandes carregadas em memória (prefira `lazy()` ou `cursor()`)
- Operações que deveriam ser feitas no banco (count, sum, avg) sendo feitas em PHP
- Actions ou repositórios fazendo múltiplas queries que poderiam ser uma só
- Serialização desnecessária em Resources (campos não utilizados pelo frontend)

### Frontend
- Componentes re-renderizando desnecessariamente (falta `useMemo`, `useCallback`)
- Chamadas à API duplicadas ou sem debounce
- Dados pesados carregados sem paginação
- Bundle size — imports pesados que poderiam ser lazy-loaded

### API
- Endpoints retornando dados excessivos (sem paginação, sem filtro de campos)
- Falta de cache headers em respostas estáticas
- Throttle adequado em endpoints públicos

3. Para cada problema encontrado, forneça:
   - **Impacto**: alto/médio/baixo
   - **Código atual**: trecho problemático
   - **Solução proposta**: código corrigido
   - **Ganho esperado**: descrição qualitativa da melhoria

4. Priorize as correções por impacto (alto → baixo)
