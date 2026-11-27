# AGENTS.md — Sistema de Questões

Este arquivo se aplica a todo o repositório. Ele é a referência operacional para agentes de desenvolvimento. Instruções mais específicas em arquivos `AGENTS.md` de subdiretórios, se vierem a existir, prevalecem apenas no respectivo escopo.

Convenções deste documento:

- **Fato identificado:** comportamento comprovado no código, nos manifests ou na configuração versionada.
- **Obrigatório:** regra que deve ser seguida em futuras demandas.
- **Recomendação:** prática preferencial, subordinada ao escopo da demanda e à arquitetura existente.
- **Pendente/risco:** inconsistência observada, não corrigida durante a criação deste documento.

## 1. Contexto do projeto

### Finalidade e arquitetura

O **Sistema de Questões** é uma aplicação web para estudo e acompanhamento de desempenho em questões de concursos. É um monólito Laravel server-rendered, organizado principalmente em MVC, com Eloquent para persistência, Blade no frontend e uma camada de serviços usada apenas em regras específicas. Não há API pública, repositórios, policies ou frontend SPA identificados.

Principais módulos comprovados:

- autenticação, recuperação de senha, verificação de e-mail e perfil;
- catálogo de órgãos, bancas, anos, cargos, matérias, grupos de assuntos, assuntos, questões e alternativas;
- resolução de questões com filtros salvos, histórico de respostas e paginação;
- revisão espaçada pelo sistema Leitner, com cinco caixas e intervalos de 1, 3, 7, 15 e 30 dias;
- caderno de erros e reaplicação de questões pendentes;
- grade semanal por grupos de estudo e matérias;
- meta de aprovação por cargo/anos, com progresso calculado pelo saldo de acertos menos erros;
- domínio por banca, também calculado pelo saldo entre acertos e erros;
- criação, execução, pausa, retomada, finalização, estatísticas e exportação JSON de simulados;
- dashboards e relatório de curva de aprendizagem;
- área administrativa para edição de questões, upload de imagens e importação de provas em JSON.

Responsabilidades principais:

- `app/Http/Controllers`: orquestra requisições, consultas e respostas Blade. Alguns controllers ainda concentram regras de negócio.
- `app/Services/LeitnerService.php`: agenda revisões e movimenta questões entre caixas.
- `app/Services/MetaAprovacaoService.php`: cria/exclui metas e recalcula percentual/rank.
- `app/Services/DominioBancaService.php`: atualiza acertos, erros e universo de questões por banca acompanhada.
- `app/Services/JsonQuestoesImporter.php`: importa a hierarquia da prova e substitui alternativas por código de questão.
- `app/Models`: entidades e relacionamentos Eloquent.
- `resources/views`: interface Blade, layouts e componentes.
- `routes/web.php` e `routes/auth.php`: todas as rotas HTTP identificadas; não existe `routes/api.php`.

### Regras de negócio já existentes

- Dados pertencentes ao usuário, como filtros, metas, grade, caderno, domínio de banca e simulados, devem sempre ser limitados pelo `user_id` autenticado.
- Recursos de outro usuário devem retornar 403 ou não ser encontrados. Preserve as verificações explícitas existentes até que uma política de autorização seja adotada de forma intencional.
- O acesso administrativo depende do middleware `admin` e do campo `users.is_admin`.
- Uma meta ativa cria também um filtro salvo; ao excluir a meta, seu filtro associado é excluído.
- Os ranks da meta são: `ruim` (0%), `regular` (1–15%), `bom` (16–35%), `excelente` (36–70%) e `aprovado` (71–100%).
- A porcentagem de meta e de domínio de banca é limitada a 0–100 e usa `(acertos - erros) / total de questões`.
- Um erro no Leitner retorna a questão à caixa 1; um acerto avança uma caixa, até a caixa 5.
- A importação usa `questoes.codigo` como identidade, cria entidades auxiliares quando necessário e recria as alternativas da questão importada.
- O cadastro público está desabilitado nas rotas, embora controller, view e testes de cadastro ainda existam.

**Obrigatório:** não inventar funcionalidades ou alterar essas regras por inferência. Se uma demanda não definir uma regra com impacto em autorização, pontuação, revisão ou integridade de dados, pedir esclarecimento antes da parte afetada.

## 2. Stack tecnológica

| Área | Exigido/versionado | Instalado localmente em 27/11/2026 |
|---|---|---|
| PHP | `^8.2` | 8.2.35 |
| Laravel | `^12.0` | 12.74.0 |
| Composer | lockfile v2 | 2.5.8 |
| Node.js | não fixado no projeto | 24.18.0 |
| npm | lockfile v3 | 11.16.0 |
| PHPUnit | `^11.5` | 11.5.58 |
| Laravel Breeze | `^2.3` (desenvolvimento) | 2.4.2 |
| Laravel Pint | `^1.27` (desenvolvimento) | 1.30.4 |
| Vite | `^8.0.0` | 8.0.16 |
| Tailwind CSS | `^3.1.0` | 3.4.19 |
| Alpine.js | `^3.15.12` | 3.15.12 |

Frontend efetivamente encontrado:

- Blade como camada de apresentação;
- Bootstrap 5.3.3, Font Awesome 6.7.2 e Tom Select 2.3.1 carregados por CDN nos layouts principais;
- tema escuro Bootstrap e estilos globais em `resources/views/components/global-styles.blade.php`;
- TinyMCE 7 para conteúdo rico e Chart.js para gráficos, ambos carregados por CDN;
- Tailwind CSS, Alpine.js e componentes herdados do Breeze empacotados por Vite;
- PostCSS e Autoprefixer no pipeline de assets.

Banco de dados:

- `.env.example` aponta para MySQL; nesse caso é necessária a extensão `pdo_mysql`;
- o ambiente local analisado reporta SQLite e possui `database/database.sqlite` ignorado pelo Git;
- os testes usam SQLite em memória por `phpunit.xml` e exigem `pdo_sqlite`/`sqlite3`;
- cache, fila e sessão usam banco por padrão no ambiente da aplicação; nos testes usam drivers isolados (`array`/`sync`).

Dependências PHP são geridas por Composer e dependências frontend por npm. Não adicionar CDN ou pacote sem justificar sua necessidade e verificar se uma solução já presente atende ao caso.

## 3. Estrutura do projeto

| Caminho | Responsabilidade e destino de novas alterações |
|---|---|
| `app/Http/Controllers` | Fluxo HTTP e composição de respostas. Controllers novos devem permanecer finos quando houver regra reutilizável. |
| `app/Http/Controllers/Auth` | Fluxos do Breeze; preserve rate limiting, regeneração/invalidação de sessão e validações. |
| `app/Http/Requests` | Form Requests existentes; prefira-os quando a validação for extensa ou reutilizada. |
| `app/Http/Middleware` | Middleware customizado; atualmente contém `IsAdmin`. |
| `app/Models` | Entidades Eloquent, casts, atributos preenchíveis e relacionamentos. |
| `app/Services` | Regras de domínio reutilizáveis ou transacionais; não criar camadas abstratas sem necessidade comprovada. |
| `app/View/Components` | Componentes de layout legados do Breeze. |
| `routes/web.php` | Rotas autenticadas, administrativas e dos módulos funcionais. |
| `routes/auth.php` | Rotas de autenticação. Cadastro está intencionalmente comentado. |
| `resources/views` | Views Blade por módulo e componentes visuais reutilizáveis. |
| `resources/css`, `resources/js` | Entradas do Vite; hoje contêm Tailwind e inicialização do Alpine. |
| `database/migrations` | Histórico estrutural do banco; há 39 migrations. |
| `database/seeders` | Importação de JSON, usuário inicial, grupos de assunto e dados opcionais de simulado. |
| `database/imports` | Provas versionadas em JSON consumidas pelo seeder/importador. |
| `database/factories` | Factory de usuário usada pelos testes. |
| `tests/Feature`, `tests/Unit` | Testes PHPUnit. Novos testes devem acompanhar o módulo alterado. |
| `config` | Configuração Laravel baseada em variáveis de ambiente. Não inserir segredos. |
| `public`, `storage` | Entrada pública e arquivos gerados/uploadados; não versionar artefatos de runtime. |

Não há uma camada `Repositories`. Use Eloquent e query builder como o código atual. Crie um Service quando a regra for compartilhada, transacional ou não pertencer naturalmente a um único controller/model.

## 4. Ambiente de desenvolvimento (Windows/PowerShell)

### Pré-requisitos

- PHP 8.2 ou superior compatível com `composer.json`;
- Composer;
- Node.js compatível com Vite 8 e npm;
- MySQL ou SQLite, conforme a configuração local.

Extensões observadas no CI/ambiente e necessárias conforme o fluxo: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `libxml`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `xmlwriter` e `zip`; `pdo_mysql` para MySQL; `pdo_sqlite` e `sqlite3` para SQLite. O CI também habilita `pcntl`, relevante ao ambiente Linux, mas não normalmente disponível/necessário no Windows.

Verificação no PowerShell:

```powershell
php --version
php -m
php -m | Select-String 'PDO|pdo_mysql|pdo_sqlite|sqlite3|mbstring|openssl|xml|curl|zip'
composer check-platform-reqs
node --version
npm --version
```

### Instalação e configuração manual segura

```powershell
composer install
npm ci

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
}

php artisan key:generate
```

Depois, configure apenas o `.env` local, nunca o versionado. Não copie credenciais reais para documentação. O `.env.example` precisa ser revisado antes de ser usado em ambientes compartilhados porque contém uma senha de exemplo não vazia.

Para SQLite:

```powershell
php -m | Select-String 'pdo_sqlite|sqlite3'
if (-not (Test-Path database/database.sqlite)) {
    New-Item database/database.sqlite -ItemType File
}
```

Para MySQL, confirme `pdo_mysql`, crie o banco fora da aplicação e configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` no `.env` local.

Somente após confirmar a conexão e o banco alvo:

```powershell
php artisan migrate
```

O `DatabaseSeeder` executa `JsonQuestoesSeeder` e `UserSeeder`. Só semear quando a demanda autorizar a inserção desses dados:

```powershell
php artisan db:seed
```

`SimuladoFinalizadoSeeder` e `GrupoAssuntoSeeder` existem, mas não são chamados pelo `DatabaseSeeder`; execute-os apenas intencionalmente com `--class` e autorização para modificar o banco.

O script `composer setup` instala dependências, gera chave, executa `migrate --force` e gera o build. **Não o execute automaticamente**, pois mistura instalação com alteração do banco.

### Execução, testes e build

Fluxo completo (servidor, worker de fila, Pail e Vite):

```powershell
composer run dev
```

Fluxo separado, útil no Windows:

```powershell
php artisan serve
npm run dev
```

Também existem `start-laravel-serve.bat` e `start-npm.bat`. Para uploads públicos, confirme a existência do link antes de executar `php artisan storage:link`.

```powershell
composer test
php artisan test --filter NomeDoTeste
./vendor/bin/pint --test
npm run build
```

Use `./vendor/bin/pint` para formatar PHP quando a demanda autorizar alterações. Não há script de lint JavaScript/CSS nem ferramenta de análise estática PHP configurados.

## 5. Padrões de desenvolvimento

### Nomenclatura e estilo

- Seguir PSR-4: classes/arquivos em `PascalCase`, métodos e variáveis em `camelCase` e tabelas/colunas em `snake_case`.
- Manter o vocabulário do domínio em português (`Questao`, `Simulado`, `CadernoErro`); nomes do framework permanecem em inglês.
- Controllers terminam em `Controller`, serviços em `Service`, testes em `Test` e migrations descrevem a alteração em `snake_case`.
- Respeitar `.editorconfig`: UTF-8, LF, quatro espaços (dois em YAML) e newline final.
- Preservar as convenções Laravel/Pint já configuradas. Não fazer uma tradução ou renomeação ampla fora do escopo.

### Organização e implementação

- Reutilizar Services e componentes existentes antes de criar duplicações.
- Manter regras de negócio fora das views. Quando extraídas de controller, fazê-lo apenas no escopo necessário e com teste de regressão.
- Usar eager loading (`with`, `load`) quando relações forem lidas em coleção; revisar consultas em loops para evitar N+1.
- Usar route model binding onde já adotado e sempre validar a propriedade do usuário.
- Usar `Request::validate` em casos simples e Form Request em validações maiores/reutilizadas.
- Nunca confiar em validação apenas no navegador. Normalizar e validar IDs, enums, arrays, arquivos e HTML no backend.
- Não capturar exceções para ocultá-las. Registrar contexto seguro e retornar mensagem adequada sem stack trace, SQL ou segredo.
- Usar transação para gravações dependentes, como importações, criação de simulado e alterações que abrangem várias tabelas.
- Preservar compatibilidade com os fluxos atuais e não introduzir Repository, CQRS, API ou SPA sem demanda comprovada.

## 6. Banco de dados

Entidades centrais: `users`, `orgaos`, `bancas`, `anos`, `cargos`, `materias`, `grupos_assunto`, `assuntos`, `textos_complementares`, `questoes` e `alternativas`. Dados do usuário incluem `historico_respostas`, `progresso_questoes`, `caderno_erros`, `filtros_salvos`, `metas_aprovacao`, `dominio_bancas`, `grupos_estudo`, `grupo_materia`, `grade_estudos`, `simulados` e `simulado_questoes`.

**Obrigatório:**

- realizar toda alteração estrutural por nova migration;
- não editar migration já compartilhada/executada sem autorização explícita;
- preservar dados existentes, chaves estrangeiras, índices, unicidade e comportamento de exclusão;
- usar transações quando operações dependentes precisarem ser atômicas;
- avaliar compatibilidade com MySQL e SQLite, pois desenvolvimento/configuração e testes divergem;
- evitar N+1, consultas repetidas e carregamento de coleções sem limite;
- nunca executar automaticamente comandos destrutivos ou exclusões em massa.

É proibido sem autorização explícita e confirmação do banco alvo:

```text
php artisan migrate:fresh
php artisan migrate:reset
php artisan db:wipe
php artisan migrate:rollback
```

Também são proibidos `DROP`, `TRUNCATE`, exclusão de tabelas/registros existentes e reset de dados por endpoint ou script. O endpoint existente de reset do dashboard é uma operação destrutiva e exige cuidado específico em alterações futuras.

## 7. Frontend e experiência do usuário

O padrão visual predominante é Blade + Bootstrap em tema escuro, com cards de raio amplo, superfícies escuras, bordas discretas, mensagens Bootstrap, ícones Font Awesome, tabelas responsivas e breakpoints `sm`/`md`/`lg`. `components.header` centraliza a navegação e `components.global-styles` centraliza tokens/ajustes visuais. Algumas views ainda usam componentes/classes Tailwind herdados do Breeze.

**Obrigatório:**

- estender `layouts.app` em páginas autenticadas e `layouts.guest` em autenticação, seguindo o padrão efetivamente usado pelo módulo;
- reutilizar componentes, estilos globais, cards, alerts, modais e controles existentes;
- preservar tema escuro, responsividade e largura máxima adotada;
- associar `label` e campo, manter foco visível, texto alternativo/`aria-label` e navegação por teclado;
- mostrar erros próximos ao campo e preservar `old()`; usar flash `success`, `error` ou `status` de forma consistente;
- pedir confirmação clara antes de ações destrutivas;
- não depender apenas de cor para comunicar acerto, erro, status ou progresso;
- manter JavaScript progressivamente aprimorado quando o fluxo puder funcionar no servidor.

Evite adicionar mais CSS/JavaScript inline. Em mudanças maiores, prefira componentes/arquivos compartilhados, sem refatorar as views existentes fora do escopo. Não misture novas classes Tailwind em telas Bootstrap sem uma razão clara.

## 8. Segurança

**Regras obrigatórias:**

- manter `auth` para dados privados e `admin` para ações administrativas;
- verificar `user_id` em toda leitura e mutação de recursos pertencentes ao usuário; route model binding não substitui autorização;
- nunca permitir que um perfil comum escolha ou altere privilégios administrativos;
- validar e autorizar no backend antes de gravar;
- usar Eloquent/query builder ou bindings, nunca interpolar entrada em SQL;
- manter `@csrf` em formulários mutáveis e respeitar os verbos HTTP;
- escapar saída com `{{ }}` por padrão;
- tratar `{!! !!}`, TinyMCE, HTML importado e `tabela_html` como superfícies de XSS: somente renderizar HTML confiável/sanitizado;
- validar upload por tipo, MIME, tamanho e autorização; não confiar em extensão/nome original;
- manter senhas com hashing do Laravel e preservar regeneração/invalidação de sessão nos fluxos de autenticação;
- guardar segredos apenas em variáveis de ambiente/secret manager; nunca em PHP, Blade, JavaScript, fixtures ou documentação;
- não registrar senhas, tokens, conteúdo sensível, headers de autenticação ou variáveis de ambiente;
- retornar mensagens seguras ao usuário e manter detalhes técnicos apenas em log protegido.

Riscos conhecidos que não devem ser copiados para novas implementações:

- o perfil atual aceita `is_admin` de um usuário autenticado, criando risco de elevação de privilégio;
- uma credencial/chave de serviço do editor rico está embutida no layout;
- `.env.example` contém uma senha de banco não vazia;
- conteúdo de questões/alternativas é renderizado como HTML cru em partes da interface;
- `AdminQuestaoController::update` grava vários campos sem uma validação backend declarada.

Não exponha os valores identificados. A correção desses riscos depende de uma demanda própria e testes específicos.

## 9. Testes e validações

Framework: PHPUnit 11.5 via `php artisan test`. `phpunit.xml` usa SQLite `:memory:`, cache/sessão `array`, fila `sync` e mail `array`. A suíte possui 29 testes declarados, concentrados em autenticação/perfil, importação administrativa e domínio de banca; os principais fluxos de resposta, Leitner, caderno, grade, metas, relatórios e simulados têm pouca ou nenhuma cobertura automatizada identificada.

Estado observado em 27/11/2026:

- **22 aprovados, 7 falhos, 60 assertions**;
- falha de atualização de senha por atribuição em massa no `User`;
- duas falhas de cadastro porque as rotas estão desabilitadas;
- quatro falhas de perfil por divergência entre testes Breeze e as rotas/controller atuais (redirect e exclusão de conta).

Nunca declarar a suíte verde enquanto essas falhas persistirem. Diferenciar falhas preexistentes de regressões causadas pela demanda.

Para cada mudança:

1. identificar critérios de aceitação e cenários impactados;
2. cobrir fluxo principal, alternativas, entrada inválida e autorização;
3. testar isolamento entre usuários e acesso administrativo quando aplicável;
4. testar MySQL/SQLite quando houver SQL ou migration dependente de driver;
5. executar primeiro testes focados e depois a suíte pertinente;
6. executar Pint em modo de verificação e build frontend quando afetados;
7. informar comandos, quantidade/resultados e qualquer teste não executado.

O workflow de CI testa PHP 8.3, 8.4 e 8.5, mas seu gatilho de `push` referencia `master`, enquanto o repositório está em `main`; não presumir que pushes em `main` acionem a suíte.

## 10. Fluxo obrigatório de implementação

1. Compreender a solicitação e seus critérios de aceitação.
2. Ler este arquivo e qualquer instrução mais específica no diretório afetado.
3. Inspecionar o estado do Git sem sobrescrever alterações do usuário.
4. Analisar rotas, controller, model, service, migration, view e testes relacionados.
5. Buscar implementação/componente existente que possa ser reutilizado.
6. Identificar impactos em autorização, dados, compatibilidade de banco e frontend.
7. Resolver ambiguidades relevantes; perguntar antes de assumir regra de negócio, segurança ou migração de dados.
8. Elaborar um plano objetivo proporcional ao risco.
9. Implementar somente o escopo solicitado, em mudanças pequenas e coerentes.
10. Executar validações focadas, suíte pertinente, formatação e build aplicáveis.
11. Revisar o diff, procurar segredos e confirmar que alterações do usuário foram preservadas.
12. Entregar relatório claro de implementação, validação e pendências.

Quando a demanda estiver clara, prossiga sem aprovações intermediárias desnecessárias. Aprovação explícita continua obrigatória para ações destrutivas, alterações de produção, dependências, branches/commits/push e decisões ambíguas com impacto material.

## 11. Restrições obrigatórias

O agente não deve:

- alterar arquivos ou funcionalidades fora do escopo;
- inventar regras de negócio ou dados;
- introduzir/atualizar dependências sem necessidade e autorização;
- remover funcionalidade ou compatibilidade existente sem autorização;
- executar refatoração generalizada como efeito colateral;
- sobrescrever alterações do usuário ou limpar uma worktree suja;
- ignorar erros, ocultar falhas ou afirmar que um teste não executado passou;
- usar mocks/dados simulados como implementação definitiva;
- alterar `.env`, credenciais ou configuração de produção;
- executar migrations/seeders contra banco não confirmado;
- criar commit, trocar/criar branch, fazer merge/rebase ou push sem autorização;
- usar `git reset --hard`, `git checkout --`, limpeza destrutiva ou comandos equivalentes para descartar trabalho;
- corrigir riscos/inconsistências descobertos fora do escopo sem antes comunicar o impacto.

Priorize soluções simples, seguras, testáveis e compatíveis com a arquitetura atual.

## 12. Relatório de conclusão

Toda entrega futura deve informar, conforme aplicável:

- resumo e funcionalidades implementadas;
- arquivos criados e alterados;
- alterações de banco/migrations e impacto em dados;
- validações e testes executados, com resultados reais;
- critérios de aceitação atendidos;
- testes não executados e motivo;
- pendências, limitações, riscos e falhas preexistentes;
- passos manuais ou orientações adicionais.

Separar claramente **implementado**, **validado** e **pendente**.

## 13. Estado conhecido e pendências do repositório

Estes itens são fatos para orientar futuras demandas, não autorização para corrigi-los:

- `README.md`, metadados do `composer.json` e `CHANGELOG.md` ainda são majoritariamente os padrões do skeleton Laravel e não documentam o domínio.
- O README contém uma linha final com indícios de codificação inválida/NUL; há também textos mojibake visíveis em alguns arquivos.
- `.env.example` indica MySQL, enquanto o ambiente local analisado usa SQLite e os testes usam SQLite em memória.
- Há uma alteração preexistente não commitada em `composer.lock`, com atualizações patch de dependências; preservá-la e não atribuí-la automaticamente a outra demanda.
- O frontend mistura Bootstrap/CDNs com Tailwind/Breeze. `@tailwindcss/vite` 4.x está instalado, Tailwind é 3.x e o plugin não está registrado no `vite.config.js`.
- Versões de Chart.js carregadas por CDN não são uniformes entre as views.
- Há duas definições de `/dashboard`; a última rota autenticada substitui na prática a intenção de `verified` da primeira.
- Fluxos de verificação de e-mail permanecem presentes, mas o model não declara `MustVerifyEmail` e a proteção efetiva do dashboard é inconsistente.
- Controller/view/testes de cadastro e exclusão de conta permanecem no repositório, porém suas rotas estão ausentes/desabilitadas.
- `dashboard.blade.php` e componentes Breeze coexistem com o dashboard real em `dashboard/index.blade.php` e com layouts baseados em `@yield`, indicando código residual.
- Existem controllers resource com métodos de scaffold vazios ou sem implementação útil; não assumir que todas as 108 rotas listadas representam funcionalidades completas.
- Não foram identificadas policies, API, análise estática, lint frontend ou testes end-to-end.
- A suíte automatizada tem sete falhas conhecidas descritas na seção de testes.

Antes de trabalhar em qualquer item pendente, confirmar escopo, comportamento desejado e estratégia de compatibilidade/migração.
