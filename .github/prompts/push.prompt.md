# Push da Tarefa

Finalize a tarefa atual: commit, push e prepare para pull request.

## Instruções

1. **Verificar estado**
   ```bash
   git status
   git diff --stat
   ```
   Mostre ao usuário os arquivos alterados e peça confirmação para prosseguir.

2. **Executar QA rápido**
   - `./vendor/bin/pint` (auto-fix lint)
   - `./vendor/bin/pest` (rodar testes)
   - Se os testes falharem, pare e reporte — não faça push com testes quebrando

3. **Stage e Commit**
   - Stage todas as alterações relevantes (`git add -A`)
   - Crie a mensagem de commit em inglês seguindo Conventional Commits:
     ```
     {type}({scope}): {descrição curta}

     {corpo opcional com detalhes}
     ```
   - Tipos: `feat`, `fix`, `refactor`, `test`, `chore`, `docs`, `perf`
   - Scope: o domínio afetado (ex: `auth`, `user`, `infra`)
   - Exemplo: `feat(auth): add password reset flow`
   - Se houver múltiplas mudanças desconectadas, sugira separá-las em commits distintos

4. **Push**
   ```bash
   git push origin HEAD
   ```
   - Se for o primeiro push da branch, use `git push -u origin HEAD`

5. **Resumo para PR**
   Gere uma sugestão de descrição de PR com:
   - **O que foi feito**: resumo das mudanças
   - **Como testar**: passos para validar manualmente
   - **Checklist**: migrations, testes, lint
