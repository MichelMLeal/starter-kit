# QA — Garantia de Qualidade

Execute uma verificação completa de qualidade antes de considerar a tarefa pronta para review.

## Instruções

Execute cada etapa e reporte o resultado:

### 1. Lint
```bash
./vendor/bin/pint --test
```
- Se falhar, corrija com `./vendor/bin/pint` e liste o que foi ajustado

### 2. Testes
```bash
./vendor/bin/pest
```
- Todos os testes devem passar
- Se algum falhar, investigue e corrija

### 3. Build do Frontend
```bash
npm run build
```
- Deve compilar sem erros ou warnings

### 4. Checklist Manual
Verifique no código da branch atual (`git diff main...HEAD`):

- [ ] Novas rotas têm middleware adequado (`auth:sanctum`, `throttle`)
- [ ] Novas migrations têm método `down()` funcional
- [ ] Novos Models estão em `app/Domain/` com relações, `$fillable`, `$casts`
- [ ] Repository interfaces criadas no Domain, implementações no Infrastructure
- [ ] Bindings registrados no Service Provider
- [ ] Form Requests validam todos os campos
- [ ] API Resources não expõem dados sensíveis (password, tokens)
- [ ] Testes cobrem happy path + erros de validação + autenticação
- [ ] Sem `dd()`, `dump()`, `console.log()` esquecidos no código
- [ ] Sem credenciais ou secrets hardcoded

### 5. Resumo
Apresente um relatório:
```
✅ Lint: OK
✅ Testes: 19 passed
✅ Build: OK
✅ Checklist: 10/10
🟢 Pronto para review
```

Ou liste os itens que precisam de atenção antes de prosseguir.
