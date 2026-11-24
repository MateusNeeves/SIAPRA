# SIAPRA — recuperação por senha temporária

## 1. Implementation Summary

Implementação disponível no working tree, sem commit, push, implantação ou alteração de `.env` real. Mantém a senha definitiva durante a emissão, envia uma nova senha temporária por solicitação elegível e restringe cada sessão temporária até a redefinição. A inspeção inicial encontrou working tree limpo e confirmou o diagnóstico.

Estado técnico: **COMPLETED para revisão**, sujeito aos pré-requisitos de implantação abaixo. Isso não declara validação de SMTP institucional nem liberação para produção.

## 2. Files Changed

| Arquivo | Finalidade |
|---|---|
| `database/migrations/2026_11_08_000001_add_temporary_password_to_users.php` | Dois campos nullable; cadastro idempotente das ações de auditoria; rollback preserva ações referenciadas |
| `app/Models/User.php` | Oculta hash e expiração temporários; cast datetime; campos fora de fillable |
| `app/Services/RecuperacaoSenhaService.php` | Geração, emissão, transporte, compensação, vínculo, autenticação, conclusão, troca voluntária e auditoria |
| `app/Mail/SenhaTemporaria.php` | Mailable síncrono, sem fila |
| `resources/views/emails/senha-temporaria.blade.php` | Conteúdo institucional sem username nem senha no assunto |
| `app/Http/Controllers/Auth/RecuperacaoSenhaController.php` | Solicitação, limitação, Timebox e redefinição própria |
| `app/Http/Middleware/RestringirSessaoTemporaria.php` | Restrição e encerramento de sessões temporárias inválidas |
| `app/Http/Requests/Auth/LoginRequest.php` | Integração preservando rate limiter e mensagem genérica |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Destino obrigatório após login temporário |
| `app/Http/Controllers/ProfileController.php` | Troca voluntária sob lock, senha atual obrigatória e tratamento sanitizado de erros |
| `app/Http/Controllers/UsersController.php` | Lock e cancelamento na alteração administrativa de senha |
| `bootstrap/app.php` | Middleware web após CSRF e antes de auth; sem AuthenticateSession |
| `routes/auth.php` | GET/POST de solicitação e GET/POST de redefinição |
| `resources/views/auth/login.blade.php` | Link de recuperação |
| `resources/views/auth/esqueci-senha.blade.php` | Formulário por login e resposta genérica |
| `resources/views/auth/senha-obrigatoria.blade.php` | Nova senha, confirmação e logout |
| `config/recuperacao.php` | Validade, limites, piso, fuso e endereço bloqueado |
| `config/mail.php` | Timeout SMTP configurável |
| `.env.example` | Exemplos sem segredos e requisitos de sessão/cache |
| `tests/Feature/RecuperacaoSenhaTest.php` | Testes HTTP, serviço, migration, rollback e concorrência PostgreSQL |
| `tests/Support/recovery-worker.php` | Processo independente para testar locks reais |
| `docs/RECUPERACAO-SENHA.md` | Relatório, evidências e implantação |

## 3. Requirement Mapping

| Requisito | Implementação / evidência |
|---|---|
| Solicitar somente por username, sem enumeração | Rotas `/esqueci-senha`; mesma mensagem para inexistente, inelegível, limitado ou falha |
| Senha temporária institucional, sem links/tokens URL | Mailable com senha de 12 caracteres; nenhuma rota de token |
| Senha definitiva preservada na emissão | Serviço grava apenas hash temporário e expiração; teste verifica senha original |
| Aleatoriedade e hash | `random_int`, alfabeto sem 0/O/1/l/I; `Hash::make` explícito |
| Expiração configurável | 30 minutos por padrão, validada em login, middleware e conclusão |
| Apenas duas novas colunas | Migration nullable; nenhuma tabela de recuperação ou flag global |
| Estado por sessão | `recuperacao_senha` contém ID e HMAC-SHA256 do hash; comparação `hash_equals` |
| Normal não bloqueada por recuperação pendente | Ausência de marcador passa; teste HTTP |
| Temporária nunca convertida por cancelamento/expiração | Marcador inválido encerra autenticação e invalida sessão; teste web/JSON |
| Substituição e cancelamento | Nova emissão troca hash; login definitivo e trocas de senha limpam campos sob lock |
| Login tradicional primeiro | Sob lock, `Auth::guard('web')->attempt` precede verificação temporária; limiter original compartilhado |
| Remember-me | Desativado: login definitivo e temporário usam false; middleware global remove recaller antigo antes da resolução do usuário; conclusão mantém rotação do token |
| Restrição backend inclusive escrita/admin | Allowlist de método e nome de rota; teste PUT administrativo bloqueado |
| CSRF e middleware auth/permissões | Restrição após StartSession/ShareErrors/CSRF e antes de auth; CSRF testado com resposta 419 sem logout |
| Redefinição própria | Controller/rotas próprios, confirmação, mínimo 8 e rejeição da própria temporária |
| Persistência antes de liberar sessão | Transação com revalidação sob lock; marcador removido somente após retorno do commit |
| Revogação normal e temporária | Exclusão explícita das outras sessões persistidas na mesma transação; HMAC invalida sessões temporárias |
| Falha de envio sem apagar geração nova | Compensação compara hash sob lock; testes sequencial e concorrente |
| SMTP fora da transação | Emissão retorna do DB::transaction antes de Mail::send |
| Sem transporte de logging | Allowlist de transportes reais; log, array, compostos e desconhecidos recusados antes da geração |
| Limites configuráveis | 3/login/h e 10/IP/h; dois locks por chave, ordem fixa, aquisição imediata e falha fechada na contenção |
| Piso de resposta | Timebox de 2500 ms; configurado em zero apenas na suíte funcional |
| Administradores legítimos | Nenhuma restrição de classe; bloqueio exato de `admin@admin.com`, confirmado no seeder |
| Auditoria sem credenciais | Ações fixas, ID de usuário, tipo e horário; FKs verificadas nos testes |
| Sem normalização de dados legados | Igualdade de username preservada; nenhum acesso ao banco persistente para investigar cadastros |
| Isolamento | Identidade do cluster verificada antes de DDL; sem RefreshDatabase nem migrations gerais |

## 4. Architecture

O estado persistente é a dupla `temp_password`/`temp_password_expires_at`. O hash com salt identifica a geração. A sessão recebe somente seu ID de usuário e um HMAC com separador de domínio `siapra-recovery:` e APP_KEY. O hash temporário não é colocado na sessão.

A restrição lê o usuário atualizado do banco a cada request temporário. Permite apenas GET `password.forced.edit`, POST `password.forced.update` e POST `logout`. Demais rotas devolvem redirect ou 403 JSON. Marcadores nulos, adulterados, expirados, cancelados ou substituídos encerram a sessão, com 401 JSON ou retorno ao login. Revalidação que falhe por corrida dentro da conclusão também encerra a sessão no controller.

Login, emissão, compensação, conclusão e alterações de senha coordenam a mesma linha com `lockForUpdate`. O login definitivo é verificado dentro desse lock para não cancelar uma geração com base em leitura obsoleta. A conclusão revalida o vínculo e a expiração, grava senha e remember token, limpa recuperação, revoga sessões e audita atomicamente. Só depois remove o marcador, regenera ID/CSRF e libera acesso.

A revogação foi antecipada para dentro da transação, adaptação deliberada para não liberar acesso após uma falha parcial na exclusão de sessões. Exige `SESSION_DRIVER=database`, com SESSION_CONNECTION vazio ou igual à conexão padrão. Configuração incompatível recusa a conclusão com mensagem de suporte.

`AuthenticateSession` foi removido do grupo web na remediação M2, assim como todos os usos de `password_hash_web`. Edição administrativa e troca voluntária não passam a encerrar sessões normais por comparação de hashes. A conclusão mantém a exclusão explícita das outras sessões persistidas e a rotação do remember token. O HMAC permanece suficiente para impedir que sessões temporárias canceladas, substituídas ou expiradas se tornem definitivas. O DatabaseSessionHandler nativo atualiza, sem reinserir, uma sessão existente que tenha sido excluída durante um request; esse caso tem teste próprio.

POST de redefinição por sessão normal sem marcador retorna ao dashboard antes da validação/persistência e mantém a autenticação. Marcador presente, inclusive nulo ou inválido, continua sujeito a encerramento.

## 5. Email Handling

Envio síncrono pelo Laravel, após persistência. Timeout SMTP padrão de 10 segundos. Falha detectada tenta limpar apenas a geração que falhou. Falha da própria compensação gera aviso sanitizado; a credencial expira normalmente. Não há transação distribuída: SMTP pode aceitar mensagem e ainda ocorrer timeout, e aceite não comprova entrega. E-mails podem chegar fora de ordem; só a última geração vale.

São admitidos transportes conhecidos de entrega: smtp, ses, ses-v2, postmark, mailgun e sendmail. Configurações compostas são recusadas, mesmo quando não contenham log, para evitar fallback inseguro. A exceção é exclusivamente `Mail::fake()` no ambiente de testes. Nenhum worker de fila foi introduzido. Eventos/listeners externos e ferramentas de observabilidade devem ser configurados para não capturar corpos de mensagens, requests de senha nem bindings SQL.

Template informa validade em America/Bahia por padrão, preservação da senha definitiva, invalidação de emissões anteriores e orientação para ignorar solicitação desconhecida. Não inclui username nem destinatário no conteúdo. Transporte real e entrega institucional **não foram testados**.

## 6. Security Considerations

- Nenhuma senha temporária é persistida em banco, sessão, cache ou logs pela implementação; existe em memória para construir o e-mail.
- Logs de falha são fixos: não encaminham mensagem, stack ou bindings da exceção de SMTP/banco.
- A limitação usa hashes das chaves e cache com locks por login/IP; indisponibilidade do cache recusa emissão com resposta genérica.
- Piso temporal reduz diferenças, mas SMTP acima de 2,5 segundos ainda permite diferenças observáveis. Não há promessa de tempo constante. Validação de formato do input ocorre antes do Timebox e não consulta a conta.
- E-mail sintaticamente válido não comprova posse/institucionalidade. A qualidade cadastral deve ser homologada pela instituição. Somente o fictício conhecido foi bloqueado.
- Revogação afeta requests futuros; não desfaz operações já autorizadas e em andamento. A exclusão alcança sessões já persistidas no instante da transação. Um login convencional concorrente é serializado pelo lock da linha do usuário: se vence a disputa, cancela a recuperação e a conclusão é recusada; se perde, a senha anterior já não é aceita (teste G). A ressalva residual é a transição de versões durante a implantação: requests atendidos pelo código anterior não passam por esse lock, por isso a ativação da nova versão exige drenagem prévia (seção 9). Sessões temporárias continuam protegidas pelo HMAC independentemente dessa janela.
- Produção requer HTTPS, APP_DEBUG=false, APP_KEY estável/protegida e cookies seguros. Não registrar bodies de formulários, e-mails ou consultas com credenciais em ferramentas adicionais.

## 7. Tests

### Aprovados

Suíte original: **23 testes, 223 asserções** (evidência anterior; resultados da remediação ao final), sem falhas, PHP 8.5.4 e PostgreSQL 18.6. Inclui migration up/down apenas nas tabelas descartáveis, emissão e substituição, inelegíveis, transporte inseguro, falha de envio, limites, login, restrições HTTP/JSON, expiração, validação, rollback, revogação, senha nova, troca voluntária e administrativa, CSRF e regeneração de sessão.

Cluster criado nesta execução em `C:/dev/SIAPRA/storage/framework/testing/recovery-pg`, bind 127.0.0.1, porta 55438, usuário siapra_test. Cada teste consulta data_directory, inet_server_port e current_user e os compara aos valores fixos antes de escrever. Credenciais/conexões da aplicação são substituídas; não se usa DB_URL nem `.env` para a conexão de testes. A infraestrutura anterior `PostgresTemporario` e seu relatório foram inspecionados; a nova suíte acrescenta verificação de identidade e não usa RefreshDatabase.

Testes usuais usam tabelas TEMP. O teste concorrente usa schema aleatório compartilhado no mesmo cluster, processos PHP independentes e observa `wait_event_type = Lock` em pg_stat_activity antes de liberar a transação. Abrange duas emissões, compensação antiga após nova emissão, conclusão após cancelamento, conclusão durante nova emissão e duas conclusões. Não são apresentados como simulações sequenciais. Schema compartilhado é removido em finally. O cluster é encerrado ao fim da execução; seus arquivos de teste permanecem ignorados pelo Git.

Também aprovados: sintaxe PHP dos arquivos alterados/criados; renderização Blade das telas via testes HTTP; rotas registradas; ordem efetiva dos middlewares; `git diff --check`. Pint aplicado somente aos arquivos PHP novos.

Com o cluster descartável iniciado e identificado, comando da suíte (PowerShell):

```powershell
$env:SIAPRA_RECOVERY_TEST='1'
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
$env:DB_URL=''
php vendor/bin/phpunit tests/Feature/RecuperacaoSenhaTest.php --do-not-cache-result
```

Sem SIAPRA_RECOVERY_TEST=1, os testes são ignorados; a variável sozinha não autoriza DDL, pois as três verificações de identidade continuam obrigatórias. Para preparar cluster novo, usar initdb/pg_ctl do PostgreSQL, somente no diretório recovery-pg acima, com usuário siapra_test e porta 55438. Nunca apontar esses testes para banco persistente. A suíte usa Mail::fake ou double que lança falha antes de enviar. Não há envio real.

### Não executados

- Suíte legada completa de Breeze e demais módulos: risco conhecido de RefreshDatabase no banco persistente; execução indiscriminada proibida.
- Homologação visual em navegador/Windows Apache e navegação por teclado: testes HTTP renderizam as telas, não comprovam aparência final.
- Análise estatística ampla de canais temporais, throughput de NAT, SMTP lento e cargas elevadas. A remediação acrescentou uma medição complementar pequena de login.
- Matriz adicional PHP 8.2 e versão exata de PostgreSQL da produção.
- E-mail real, TLS, autenticação e entrega institucional.

### Bloqueados por dependência externa

Homologação do envio e validação da configuração real de sessão/cache/SMTP de produção dependem da infraestrutura institucional. Não bloqueiam revisão do código, mas bloqueiam sua liberação para produção.

## 8. Known Issues

1. Cadastros podem conter usernames legados com maiúsculas/espaços. Não houve leitura do banco persistente; preservada a semântica atual, inclusive TrimStrings já existente. Cadastro administrativo continua usando minúsculas.
2. Troca voluntária mantém mínimo quatro; redefinição obrigatória exige oito. Dívida técnica deliberadamente mantida.
3. Edição administrativa existente redefine senha em toda edição; esse comportamento não foi reformulado. Agora também cancela a recuperação na mesma transação.
4. PHPUnit legado permanece inseguro para execução geral sem isolamento. Não alterado nesta tarefa.
5. Configuração de sessão de produção não confirmada; driver diferente ou conexão separada recusa conclusão com segurança.
6. SMTP síncrono ocupa request e pode ultrapassar o piso temporal. Observabilidade e rate limits precisam de ajuste após homologação, especialmente por NAT.
7. Requer cache compartilhado com locks. Cache array é exclusivo de testes; não fornece limites distribuídos em produção.
8. A allowlist recusa transportes customizados/compostos. Um provedor que exija integração não existente precisará de trabalho específico após definição institucional.

## 9. Deployment Requirements

- [ ] Definir provedor/mecanismo; host, porta, TLS, método de autenticação, conta remetente e permissões. Não presumir SMTP básico, Microsoft 365 ou Google.
- [ ] Confirmar firewall/conectividade, certificados CA/TLS e requisitos de autenticação. Não desabilitar verificação TLS.
- [ ] Configurar MAIL_MAILER real, MAIL_FROM_ADDRESS/NAME, credenciais protegidas e MAIL_TIMEOUT. Não usar log/array/failover para recuperação.
- [ ] Confirmar SESSION_DRIVER=database e mesma conexão padrão; tabela sessions funcional com user_id; drenar requests e encerrar sessões legadas na implantação inicial.
- [ ] Confirmar cache compartilhado, tabelas cache/cache_locks se driver database, operações atômicas e permissões. O store do RateLimiter deve ser compartilhado pelos mesmos processos. Manter operações de cache muito abaixo do lease de 10 segundos; pausa de processo ou indisponibilidade que ultrapasse o lease é risco residual de locks com TTL.
- [ ] Se houver proxy reverso, configurar proxies confiáveis para que Request::ip() represente corretamente o cliente; não confiar indiscriminadamente em X-Forwarded-For.
- [ ] Definir APP_URL HTTPS, APP_KEY, APP_DEBUG=false, cookies seguros e política de proteção/redação nos logs.
- [ ] Revisar qualidade dos e-mails e conta bootstrap; homologar destinatários institucionais controlados.
- [ ] Aplicar migration por procedimento institucional com backup, após revisão; nenhuma migration foi executada em banco persistente nesta tarefa.
- [ ] Configurar validade, limites e fuso; sincronizar relógios dos servidores.
- [ ] Recarregar cache de configuração pelo procedimento de implantação, testar login/logout, envio, redefinição e acesso administrativo em homologação.
- [ ] Homologar interface no navegador institucional e entrega real; só então liberar produção.

## 10. Final Status

**COMPLETED — implementação e verificações técnicas concluídas para revisão.** Nenhum commit, push ou implantação foi realizado. Configuração e envio reais de produção permanecem pendentes, conforme checklist. O código não está declarado liberado para produção.


# Remediação da revisão independente — M1, M2, L1–L5

## 1. Remediation Summary

Correções pontuais preservam as duas colunas, geração de senha, HMAC, emissão síncrona, compensação, transações e redefinição. Não houve alteração no funcionamento administrativo para contornar M2 nem introdução de revogação global por edição.

M1: o retorno antecipado foi removido. `Auth::attempt` executa em todos os caminhos. Um listener de `Failed` do guard web executa uma verificação fictícia quando o usuário não existe, **dentro do Timebox nativo do guard**, pois Laravel dispara esse evento antes de sair do envelope. Após falha definitiva, sempre há uma segunda verificação: hash temporário real (mesmo expirado) ou fictício. Validade/expiração só decidem autorização depois dessa operação. Login definitivo correto mantém apenas uma verificação. O remember-me nativo, mantido na remediação M1, foi posteriormente desativado pela decisão de produto N1 abaixo.

`SenhaFicticia` fornece um hash exclusivamente de trabalho, com salt/digest fixos e parâmetros do hasher configurado (bcrypt/Argon). Seu resultado nunca autoriza usuário inexistente ou credencial ausente. Não se gera um novo hash por request e não se depende apenas de sleep. A autenticação mantém o lock da linha e o cancelamento transacional.

## 2. Findings Resolution

| Achado | Status | Arquivos e evidência | Testes |
|---|---|---|---|
| M1 | RESOLVED | RecuperacaoSenhaService, SenhaFicticia, AppServiceProvider: trabalho equivalente nos quatro caminhos de falha dentro/fora do guard | Instrumentação de duas verificações; stack confirma Timebox na primeira; medição bcrypt12 |
| M2 | RESOLVED | bootstrap/app.php, AuthenticatedSessionController, RecuperacaoSenhaService: removidos AuthenticateSession e password_hash_web | Autoedição mantém acesso; outro usuário mantém sessão; exclusão explícita na conclusão; escrita tardia de sessão existente não ressuscita registro |
| L1 | RESOLVED | RecuperacaoSenhaController: ausência de marcador retorna ao dashboard antes de validação/gravação | POST normal preserva autenticação e todos os atributos do usuário |
| L2 | RESOLVED | LimitesRecuperacao e controller: locks por login/IP em ordem lexical, sem espera em chave ocupada; limites conferidos e incrementados sob ambos | Contenção em login não bloqueia outro login/IP; oito processos disputam cache compartilhado, limites por login e por IP respeitados |
| L3 | RESOLVED | User: hash e expiração em hidden | Serialização oculta ambos, acesso direto à expiração funciona, auditoria administrativa não contém temp_password |
| L4 | RESOLVED | bootstrap/app.php: prioridade CSRF > restrição > auth, mantendo sessões e ShareErrors antes | Inspeção da pilha efetiva e POST sem CSRF com marcador inválido retorna 419 sem logout; GET seguinte encerra sessão |
| L5 | RESOLVED | RecuperacaoSenhaTest e recovery-worker ampliados | Casos acima, Timebox positivo, locks explícitos, login versus cancelamento/emissão e administração versus recuperação |

## 3. Files Changed nesta remediação

- `app/Services/SenhaFicticia.php` (novo): verificação criptográfica fictícia sem credencial autenticável.
- `app/Services/LimitesRecuperacao.php` (novo): admissão atômica sob locks granulares.
- `app/Providers/AppServiceProvider.php`: listener de falha inexistente dentro do Timebox do guard.
- `app/Services/RecuperacaoSenhaService.php`: remove retorno antecipado, uniformiza segunda verificação e remove password_hash_web.
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`: remove password_hash_web.
- `app/Http/Controllers/Auth/RecuperacaoSenhaController.php`: proteção contra formulário antigo, limiter extraído e Timebox resolvido pelo container para instrumentação.
- `app/Models/User.php`: oculta expiração na serialização.
- `bootstrap/app.php`: remove AuthenticateSession e ajusta prioridade pontual de CSRF/restrição.
- `tests/Feature/RecuperacaoSenhaTest.php`: amplia regressões, instrumentação e concorrência.
- `tests/Support/recovery-worker.php`: operações de login/admin/cache em conexões independentes.
- `docs/RECUPERACAO-SENHA.md`: corrige descrição anterior e registra remediação.

## 4. Security Verification

Senha definitiva preservada na emissão, hash temporário/expiração e HMAC mantidos. Nenhum marcador inválido vira sessão normal. Sem marcador, o POST antigo não altera credenciais; com marcador inválido, encerra autenticação após CSRF. A conclusão continua validando geração/expiração sob lock e revogando sessões explicitamente na transação. Edição administrativa continua cancelando recuperação sem logout global normal. Nenhum e-mail real enviado e nenhum segredo acrescentado aos logs.

## 5. Concurrency Verification

Os processos de recuperação esperam por locks reais no PostgreSQL. Além de `wait_event_type=Lock`, o teste exige que a consulta bloqueada contenha `FOR UPDATE`, detectando remoção do lock que antes poderia ficar mascarada pela espera do UPDATE. Novos cenários: login com senha antiga esperando uma alteração/cancelamento; login definitivo esperando emissão nova e cancelando-a; edição administrativa esperando uma emissão nova e cancelando-a. Permanecem os cenários de emissão dupla, compensação antiga e conclusões concorrentes.

Para limites, oito processos PHP distintos usam tabelas cache/cache_locks no schema descartável compartilhado. Uma barreira advisory PostgreSQL permite que todos iniciem a disputa juntos; são conferidos os limites por login e IP separadamente. Contenção pode recusar solicitações antes de atingir o teto, deliberadamente, mantendo resposta genérica. Chaves totalmente independentes não compartilham lock.

## 6. Test Results

**Resultado final: 32 testes, 388 asserções, sem falhas.** Sintaxe PHP e git diff --check aprovados. Cluster descartável encerrado ao final. Ambiente: PHP 8.5.4, PostgreSQL 18.6, cluster recovery-pg em 127.0.0.1:55438, usuário siapra_test. Diretório/configuração reinspecionados antes de iniciar; os testes e cada worker verificam novamente data_directory, porta e usuário antes de escrever. Conexões explícitas substituem as da aplicação. Mail::fake/doubles continuam obrigatórios. Nenhum banco persistente, SMTP real ou suíte geral foi utilizado.

Medição complementar inicial: bcrypt custo 12, seis amostras intercaladas por caminho, medianas em ms: inexistente 512,4; sem temporária 513,3; expirada 538,7; ativa 513,0. Não há limiar temporal frágil de aprovação: a prova automatizada principal é a instrumentação das operações criptográficas e do Timebox. Valores não incluem rede/Apache e não equivalem a análise estatística de produção.

## 7. Remaining Issues

- Timing não é constante: custos legados diferentes, contenção de linha, carga, eventos adicionais e banco podem introduzir diferenças. Hashes fictícios acompanham o custo configurado; hashes antigos devem ser considerados na homologação. O canal trivial por pular verificações foi removido.
- Locks de cache têm lease de 10 segundos; paralisações além desse prazo podem invalidar a exclusão mútua. Não executar trabalho SMTP sob esses locks. Cache/limiter distribuídos devem ser consistentes e rápidos.
- A revogação explícita exclui sessões persistidas; não desfaz requests autorizados em andamento nem garante alcançar um novo ID ainda não persistido por login concorrente. Isso fica explícito após a remoção aprovada de AuthenticateSession; não há alegação de revogação por versionamento global.
- Continuam pendentes homologação visual, matriz PHP 8.2/Windows Apache de produção, configuração institucional e análise de carga. Nenhuma dessas etapas foi declarada executada.

## 8. Production Dependencies

SMTP institucional e autenticação compatível, TLS/firewall/remetente, SESSION_DRIVER=database na mesma conexão, cache compartilhado com locks, proxies confiáveis quando aplicável, HTTPS e homologação em navegador no Windows/Apache. Ver checklist anterior. A remediação não libera produção.

## 9. Final Status

Pronta para segunda revisão independente, após testes seguros registrados abaixo. Alterações mantidas no working tree, sem commit, push, merge, rebase ou PR.

Última medição complementar antes da inclusão das duas asserções explícitas de ordem/ausência de middleware: inexistente 509,7 ms; sem temporária 510,3 ms; expirada 506,2 ms; ativa 512,9 ms (bcrypt12, n=6 por caminho).

Rodada final (32 testes / 388 asserções): medianas inexistente 507,4 ms; sem temporária 512,1 ms; expirada 513,2 ms; ativa 511,7 ms. Cluster encerrado após o término da suíte.


# Remediação N1 — desativação de remember-me

## 1. Remediation Summary

**N1 RESOLVED, pronto para nova revisão independente.** Remember-me não é oferecido pelo SIAPRA: login definitivo ignora qualquer parâmetro remember e passa false ao guard; o temporário já passava false. Cookies recaller anteriores são removidos do request no início do pipeline HTTP global, antes de qualquer resolução do usuário. A resposta expira somente esse cookie, sem destruir a sessão convencional.

Sem novo guard, coluna, versionamento, alteração SMTP ou reintrodução de AuthenticateSession/password_hash_web. Alterações anteriores preservadas no working tree. Nenhum commit, push ou implantação realizado.

## 2. Root Cause

No SessionGuard instalado, user() tenta primeiro o ID da sessão e depois recaller(). Este lê o cookie do Request e chama retrieveByToken; se válido, updateSession guarda o usuário na sessão em memória. StartSession persiste no retorno do pipeline. Portanto, a autenticação pelo cookie podia acontecer antes da conclusão, e a primeira persistência depois da exclusão das sessões, sobrevivendo à rotação do token. Esse caminho não passava pelo lock de RecuperacaoSenhaService::authenticate.

## 3. Implementation

| Arquivo | Alteração N1 |
|---|---|
| `app/Services/RecuperacaoSenhaService.php` | Auth::attempt recebe false, independentemente do input remember |
| `app/Http/Middleware/DesativarRememberMe.php` (novo) | Obtém somente o nome do recaller, remove-o do Request antes de next e expira-o na resposta quando presente |
| `bootstrap/app.php` | Prepend do middleware no pipeline global, antes dos middlewares de rota |
| `tests/Feature/RecuperacaoSenhaTest.php` | Cinco novos testes de emissão, cookie válido legado, sessão convencional persistida, intercalação adversarial e ordem global; amplia disputa login/conclusão |
| `tests/Support/recovery-worker.php` | Operação login_finish para validar login definitivo concorrente com conclusão real |
| `docs/RECUPERACAO-SENHA.md` | Política atual, evidências, limites e transição |

Inspeção de emissão: únicos caminhos encontrados foram attempt no serviço, login temporário com false e Auth::login($user) no RegisteredUserController. Este último usa false por padrão no framework e suas rotas estão comentadas. LoginRequest e AuthenticatedSessionController não emitem recaller por conta própria; nenhum caminho alternativo ativo com remember=true foi encontrado. Não se alterou o cadastro desativado.

Ordem: DesativarRememberMe é o primeiro middleware global, comprovado por inspeção do Kernel e por um middleware global posterior que tenta resolver o usuário. No grupo web seguem EncryptCookies, AddQueuedCookiesToResponse, StartSession, ShareErrorsFromSession, ValidateCsrfToken, RestringirSessaoTemporaria e Authenticate (nas rotas protegidas), mantendo a precedência já validada. Criar/resolver o SessionGuard e consultar getRecallerName não chama user(); o guard consulta o mesmo Request quando posteriormente tenta ler o recaller.

## 4. Legacy Remember-Me Handling

A remoção ocorre ainda sobre o cookie recebido, antes de EncryptCookies, portanto seu conteúdo não é descriptografado nem disponibilizado ao guard. A aplicação não depende da cooperação do navegador: reenviar indefinidamente um cookie antigo não autentica. Quando o pipeline retorna resposta, Cookie::forget envia expiração usando path/domain do cookie jar Laravel, sem incluir credenciais. Cookie de sessão não é removido e nenhuma sessão normal é encerrada pela mera presença do recaller.

Cookies históricos em outro path/domain podem não ser apagados pela configuração atual; se chegarem com o nome de recaller reconhecido pelo guard, continuam sendo removidos do Request. Respostas de exceção que interrompam o retorno normal podem não carregar a expiração, mas a neutralização de entrada já ocorreu. Não há necessidade de limpar remember_token no banco para aplicar a proteção do código. A rotação existente na conclusão foi preservada como defesa adicional.

## 5. Concurrency Verification

| Cenário | Resultado |
|---|---|
| A: POST remember=1 | Credenciais corretas autenticam; nenhum recaller emitido nem remember_token criado |
| B: cookie antigo sem sessão | Redireciona ao login; viaRemember=false; não grava identidade autenticada |
| C: cookie antigo + sessão convencional | ID persistido é carregado por guard novo; acesso permitido; somente recaller expirado |
| D: intercalação N1 | Request HTTP tenta resolver usuário; cookie já ausente e usuário nulo. Outra sessão conclui recuperação antes do retorno desse request/StartSession. Após persistência, o novo request permanece anônimo, mesmo reenviando recaller |
| E: sessão persistida antiga | Teste anterior confirma exclusão e ausência de ressurreição por escrita tardia do DatabaseSessionHandler |
| F: temporária inválida | Regressões de cancelamento/substituição/expiração mantêm encerramento sem promoção |
| G: login definitivo concorrente | Processo independente espera SELECT FOR UPDATE; conclusão grava senha nova, e login com senha antiga é recusado após obter o lock |

O teste D é uma **intercalação determinística no pipeline HTTP real**, com duas sessões independentes e persistência efetiva em PostgreSQL, não uma alegação de execução simultânea em dois processos. Ele controla exatamente a janela antes da gravação de StartSession e prova que a etapa de autenticação por recaller já é inalcançável. Um controle positivo instancia o SessionGuard nativo sem a proteção HTTP e confirma que o mesmo formato de cookie válido realmente autentica; não se usa cookie inválido para obter falso resultado favorável.

O teste G e os demais testes de locks/cache continuam usando processos PHP e conexões distintas, com observação de espera e consulta FOR UPDATE. A correção não move nem retira esses locks.

## 6. Regression Verification

A suíte mantém M1 (duas verificações em falhas, Timebox do guard e medição complementar), M2 (autoedição e edição alheia sem logout global), L1 (POST antigo), L2 (locks granulares sob disputa), L3 (serialização/auditoria), L4 (CSRF antes da restrição) e L5 (concorrência). Preservados HMAC, expiração, confirmação/mínimo de senha, compensação condicional, transação da conclusão e revogação explícita. AuthenticateSession e password_hash_web permanecem ausentes do código de aplicação.

## 7. Test Results

**37 testes, 459 asserções, nenhuma falha.** Comando: `php vendor/bin/phpunit tests/Feature/RecuperacaoSenhaTest.php --do-not-cache-result`, com os overrides seguros e SIAPRA_RECOVERY_TEST descritos anteriormente. Rodada direcionada anterior: 7 testes / 140 asserções.

Ambiente PHP 8.5.4, PostgreSQL 18.6, cluster descartável `C:/dev/SIAPRA/storage/framework/testing/recovery-pg`, porta 55438, bind 127.0.0.1, usuário siapra_test. Antes de iniciar, pg_ctl confirmou cluster parado, porta sem listener e postmaster.opts/PG_VERSION foram reinspecionados. Cada teste e worker verifica identidade pelo servidor antes de escrever. Conexões persistentes da aplicação substituídas por configuração explícita; Mail::fake/doubles, sem SMTP real. Cluster encerrado após verificação.

Sintaxe dos cinco arquivos PHP envolvidos aprovada; git diff --check aprovado. Medianas complementares bcrypt12, seis amostras por caminho: inexistente 613,8 ms, sem temporária 608,4 ms, expirada 603,8 ms, ativa 601,6 ms. Valores variam com carga; não são prova de tempo constante.

Não executados: suíte geral/Breeze contra configuração persistente, navegador institucional, matriz PHP 8.2, SMTP real ou implantação Windows/Apache. Testes HTTP não equivalem à homologação visual.

## 8. Remaining Risks

O bloqueio de recaller vale para requests processados integralmente pelo código corrigido. Requests já autenticados pela versão anterior podem terminar depois da substituição de arquivos; atualizar arquivos com tráfego ativo não elimina essa janela. Sessões convencionais existentes não têm origem distinguível e são preservadas pelo código, conforme decisão de produto.

Mantém-se o limite previamente documentado para requests já autorizados antes da revogação. O login convencional concorrente foi considerado protegido na revisão final: autenticação e conclusão disputam o mesmo `SELECT ... FOR UPDATE`, e o teste G comprova a revalidação das credenciais sob lock. N1 elimina especificamente a autenticação automática sem senha pelo recaller, não introduz versionamento universal da autenticação. A janela residual decorre da transição de versões durante a implantação (requests atendidos pelo código anterior, sem esses locks) e é tratada pela drenagem descrita na seção 9.

## 9. Production Requirements / transição controlada

Procedimento operacional recomendado, **não executado**:

1. Suspender entrada de novos requests em todos os nós no proxy/Apache ou modo de manutenção controlado. Não fazer rollout misto com instâncias antigas atendendo autenticação.
2. Drenar requests/processos da versão antiga e confirmar que terminaram antes de ativar a versão corrigida. Considerar workers persistentes e OPcache na troca do código; reativar somente processos carregando a versão nova.
3. Se houver necessidade de invalidar sessões potencialmente criadas pelo defeito antes do corte, planejar uma limpeza controlada das sessões após a drenagem e antes da reabertura. Não há informação de origem confiável para selecionar apenas sessões antigas vindas de recaller; uma invalidação geral exige comunicação e novo login de todos, incluindo usuários convencionais. Não é executada automaticamente por esta alteração.
4. Limpar/rotacionar remember_token globalmente não é necessário para impedir uso dos cookies no código corrigido e não substitui a drenagem ou tratamento de sessões já criadas. Caso a instituição opte por saneamento dos tokens, fazê-lo somente na janela drenada, com procedimento aprovado/backup, sem alterar senhas definitivas. Em código legado, isso retiraria o login automático; no corrigido, tokens já são ignorados como origem de autenticação.
5. Homologar POST remember=1, cookie legado e sessão normal em navegador no Windows/Apache. Confirmar ausência de recaller de autenticação novo e expiração dos antigos. Reabrir tráfego apenas na versão nova.

Permanecem SMTP institucional, SESSION_DRIVER=database na conexão requerida, cache compartilhado com locks, configuração de proxy confiável e HTTPS/cookies. Não houve leitura ou alteração de dados persistentes nem configuração real de produção. Nenhuma operação de banco de implantação foi executada.

## 10. Final Status

**N1 RESOLVED — pronto para nova revisão técnica independente.** Implementação mínima, verificações aprovadas e procedimento de transição documentado. A funcionalidade não está declarada liberada para produção.
