# RoundGuard

Sistema de Controle de Rondas com QR Code.

Variação moderna do sistema SISVIGIA, desenvolvida como projeto full stack para portfólio.

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
- Restrições por perfil (agente vê apenas seus próprios registros)

---

## Stack

### Backend
- PHP 8.5
- Laravel 13
- PostgreSQL
- Laravel Sanctum
- Spatie Laravel Permission
- Laravel Sail (Docker)

### Frontend
- Vue 3 (Composition API)
- TypeScript
- Pinia
- Vue Router
- Tailwind CSS v4
- Vite
- html5-qrcode

---

## Requisitos

- Docker + Docker Compose
- Git

---

## Como rodar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/roundguard.git
cd roundguard

2. Suba os containers
./vendor/bin/sail up -d

Na primeira vez, se ainda não tiver as dependências:
docker run --rm -v $(pwd):/app composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

3. Acesse
http://localhost

Usuários de teste

Perfil Usuário/E-mail + Senha

Admin admin / admin@gmail.com 123456
Dev dev / dev@gmail.com 123456
Vigilante teste teste@gmail.com 123456

Perfis e permissões

Funcionalidade/Admin/Supervisor/Vigilante

Dashboard✅✅✅
Registros de Ronda✅✅✅ (apenas os próprios)
Novo registro (scan)✅✅✅
Excluir registros✅✅❌
Pontos de Ronda (CRUD)✅✅❌
Usuários (CRUD)✅❌❌

Estrutura principal

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

Scripts úteis

# Subir o ambiente
./vendor/bin/sail up -d

# Parar
./vendor/bin/sail down

# Rodar migrations + seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Frontend em desenvolvimento
./vendor/bin/sail npm run dev

# Build de produção
./vendor/bin/sail npm run build

Observações

* A câmera do celular só funciona em contexto seguro (localhost ou HTTPS).
* Em desenvolvimento via IP da rede local, use o upload de imagem do QR Code.
* O projeto usa autenticação baseada em sessão (SPA + Sanctum).

Autor
Desenvolvido por [Drakar] como projeto de portfólio full stack.