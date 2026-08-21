# RoundGuard

Sistema de Controle de Rondas com QR Code.

Variação moderna do sistema SISVIGIA, desenvolvida como projeto full stack para portfólio.

**Demo em produção:** [https://roundguard-production.up.railway.app](https://roundguard-production.up.railway.app)

---

## Sobre o projeto

O **RoundGuard** permite o gerenciamento de pontos de ronda, registro de verificações via QR Code (câmera ou imagem) e controle de usuários com diferentes níveis de acesso.

Foi construído com foco em código limpo, stack atual e estrutura próxima de um sistema real de uso interno.

---

## Funcionalidades

- Autenticação com e-mail ou nome de usuário (Laravel Sanctum)
- Controle de acesso por perfil (Admin, Supervisor e Agente)
- CRUD de Pontos de Ronda
- Geração e impressão de QR Code por ponto
- Registro de rondas via leitura de QR Code (câmera ou upload de imagem)
- Confirmação antes de salvar o registro
- Geolocalização opcional no momento do scan
- Listagem de registros com filtros (agente, ponto, mês e ano)
- Dashboard com estatísticas em tempo real
- CRUD de usuários com atribuição de roles
- Log de atividades (Spatie Activity Log)
- Restrições por perfil (agente vê apenas seus próprios registros)
- Form Requests para validação
- Testes automatizados com Pest

---

## Stack

### Backend
- PHP 8.4+
- Laravel 13
- PostgreSQL
- Laravel Sanctum
- Spatie Laravel Permission
- Spatie Laravel Activity Log
- Laravel Sail (Docker — desenvolvimento)

### Frontend
- Vue 3 (Composition API)
- TypeScript
- Pinia
- Vue Router
- Tailwind CSS v4
- Vite
- html5-qrcode

### Qualidade
- Pest (testes Feature)

### Deploy
- Railway (app + PostgreSQL)

---

## Demo

- **URL:** [https://roundguard-production.up.railway.app](https://roundguard-production.up.railway.app)

### Usuários de teste

| Perfil     | Login                         | Senha    |
|------------|-------------------------------|----------|
| Admin      | `admin` / `admin@gmail.com`   | `123`    |
| Developer  | `dev`   / `dev@gmail.com`     | `123`    |
| Agente     | `teste` / `teste@gmail.com`   | `123`    |

---

## Perfis e permissões

| Funcionalidade             | Admin | Supervisor | Agente |
|----------------------------|-------|------------|--------|
| Dashboard                  | ✅    | ✅         | ✅     |
| Registros de Ronda         | ✅    | ✅         | ✅ (apenas os próprios) |
| Novo registro (scan)       | ✅    | ✅         | ✅     |
| Excluir registros          | ✅    | ✅         | ❌     |
| Pontos de Ronda (CRUD)     | ✅    | ✅         | ❌     |
| Usuários (CRUD)            | ✅    | ❌         | ❌     |
| Log de Atividades          | ✅    | ❌         | ❌     |

---

## Requisitos (desenvolvimento local)

- Docker + Docker Compose
- Git

---

## Como rodar localmente

### 1. Clone o repositório

```bash
git clone https://github.com/SEU_USUARIO/roundguard.git
cd roundguard
```

---

### 2. Suba o ambiente com Sail

```bash
docker run --rm -v $(pwd):/app -w /app composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 3. Acesse

http://localhost

### 4. Scripts úteis

## Subir / parar
```bash
./vendor/bin/sail up -d
./vendor/bin/sail down
```

## Migrations + seeders
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

## Frontend (dev)
```bash
./vendor/bin/sail npm run dev
```

## Build de produção
```bash
./vendor/bin/sail npm run build
```

## Testes
```bash
./vendor/bin/sail pest
```

### 5. Estrutura principal
```bash
app/
  Http/
    Controllers/Api/
    Requests/
  Models/
resources/
  js/
    components/
    layouts/
    pages/
      checkpoints/
      rounds/
      users/
    router/
    stores/
tests/
  Feature/
```

### 6. Observações

## Em produção o app roda com HTTPS no Railway.
## A câmera do celular funciona melhor em contexto seguro (https://).
## Em desenvolvimento via IP da rede local, use o upload de imagem do QR Code se a câmera for bloqueada.
## Autenticação SPA com Sanctum (sessão + cookies).

### Autor - Drakar
Desenvolvido como projeto de portfólio full stack (Laravel + Vue).