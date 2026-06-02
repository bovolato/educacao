# SIGEM — Sistema Integrado de Gestão Educacional Municipal

Plataforma municipal de gestão educacional desenvolvida em Laravel 12 + Laravel Sail + Tailwind CSS.

## Stack

- **Backend:** Laravel 12 (PHP 8.4)
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Banco de dados:** MySQL 8
- **Permissões:** Spatie Laravel Permission
- **Infraestrutura:** Laravel Sail (Docker)

> O ambiente de desenvolvimento roda via **Laravel Sail** a partir do diretório `educacao/`.

## Acessos

| Serviço | URL |
|---------|-----|
| Sistema | http://localhost/ |

### Usuário padrão

| Campo | Valor |
|-------|-------|
| E-mail | admin@sigem.edu.br |
| Senha | admin@2026 |
| Perfil | super_admin |

## Como rodar

Todos os comandos rodam dentro de `educacao/`.

```bash
cd educacao

# 1. Criar o .env (primeira vez)
cp .env.example .env

# 2. Subir os containers do Sail
./vendor/bin/sail up -d

# 3. Gerar a APP_KEY
./vendor/bin/sail artisan key:generate

# 4. Rodar migrations e seeders
./vendor/bin/sail artisan migrate --force
./vendor/bin/sail artisan db:seed --force

# 5. Instalar dependências de front e buildar assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

> Dica: crie um alias `alias sail='./vendor/bin/sail'` para encurtar os comandos.

## Comandos úteis

```bash
# Parar os containers
./vendor/bin/sail stop

# Ver logs
./vendor/bin/sail logs -f

# Shell no container da aplicação
./vendor/bin/sail shell

# Rodar migrations
./vendor/bin/sail artisan migrate

# Vite em modo dev
./vendor/bin/sail npm run dev

# Limpar caches
./vendor/bin/sail artisan optimize:clear
```

## Estrutura do projeto

```
educacao/
└── educacao/                     ← Projeto Laravel (raiz do Sail)
    ├── docker-compose.yml        ← Stack do Laravel Sail
    ├── app/
    │   ├── Models/
    │   │   ├── Institucional/    (Municipio, Escola, Serie, Turno...)
    │   │   ├── Pessoas/          (Pessoa, Aluno, Professor, Responsavel...)
    │   │   └── Academico/        (Turma, Matricula, Aula, Nota, Boletim...)
    │   └── View/Components/
    ├── database/
    │   ├── migrations/           (32 migrations organizadas por bloco)
    │   └── seeders/
    └── resources/views/
        ├── auth/login.blade.php
        ├── dashboard/            (secretaria, escola, professor, portal)
        └── layouts/sigem.blade.php
```

## Perfis de acesso

| Perfil | Descrição |
|--------|-----------|
| super_admin | Acesso total ao sistema |
| secretaria_municipal | Visão macro da rede municipal |
| gestor_escolar | Gestão completa da própria escola |
| secretario_escolar | Matrículas, alunos e documentos |
| coordenador | Acompanhamento pedagógico |
| professor | Lançamento de frequência e notas |
| aluno | Portal de consulta pessoal |
| responsavel | Acompanhamento do(s) aluno(s) |
| almoxarifado | Controle de materiais |
| transporte | Gestão de rotas |

## Fases de desenvolvimento

- ✅ **Fase 1** — Docker + Laravel + Auth + Usuários + Perfis + Banco de dados (concluída)
- 🔲 **Fase 2** — Matrícula completa, enturmação, transferência
- 🔲 **Fase 3** — Diário de classe, frequência, notas, boletim
- 🔲 **Fase 4** — Portal do aluno/responsável
- 🔲 **Fase 5** — Transporte, materiais, relatórios municipais
