# SIGEM — Sistema Integrado de Gestão Educacional Municipal

Plataforma municipal de gestão educacional desenvolvida em Laravel 12 + Docker + Tailwind CSS.

## Stack

- **Backend:** Laravel 12 (PHP 8.4-FPM)
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Banco de dados:** MySQL 8
- **Permissões:** Spatie Laravel Permission
- **Infraestrutura:** Docker Desktop + Nginx

## Acessos

| Serviço | URL |
|---------|-----|
| Sistema | http://localhost/ |
| phpMyAdmin | http://localhost:8080/ |

### Usuário padrão

| Campo | Valor |
|-------|-------|
| E-mail | admin@sigem.edu.br |
| Senha | admin@2026 |
| Perfil | super_admin |

## Como rodar

```bash
# 1. Subir os containers
docker-compose up -d

# 2. Aguardar o MySQL inicializar (~30s na primeira vez)

# 3. Rodar migrations e seeders
docker exec sigem_app php artisan migrate --force
docker exec sigem_app php artisan db:seed --force
```

## Comandos úteis

```bash
# Parar os containers
docker-compose stop

# Ver logs
docker-compose logs -f app

# Acessar o container PHP
docker exec -it sigem_app bash

# Rodar migrations
docker exec sigem_app php artisan migrate

# Rebuild de assets (Tailwind/Vite)
docker exec sigem_app npm run build

# Limpar caches
docker exec sigem_app php artisan optimize:clear
```

## Estrutura do projeto

```
educacao/
├── docker-compose.yml
├── docker/
│   ├── nginx/default.conf
│   ├── php/Dockerfile
│   └── mysql/init.sql
└── src/                          ← Projeto Laravel
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
