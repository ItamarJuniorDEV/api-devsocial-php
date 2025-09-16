# Política de Segurança

## Escopo

Este repositório (`api-devsocial-php`).

## Como reportar

Se você encontrar uma vulnerabilidade, por favor não abra uma issue pública. Mande um email direto pra cdajuniorf@gmail.com com:

- Descrição do problema
- Passo a passo pra reproduzir
- Impacto estimado (se souber)

Respondo o mais rápido que conseguir.

## Versões suportadas

Apenas a branch `master`. Não mantenho releases antigas.

## Boas práticas adotadas neste projeto

- Autenticação por token via Laravel Sanctum, sem sessão de cookie pra rotas da API.
- Validação de entrada via Form Requests em todos os endpoints que recebem payload.
- Autorização por Policies (`PostPolicy`) pra ações sensíveis em recursos do usuário.
- Rate limit nas rotas de autenticação (`throttle:auth`) e nas rotas autenticadas (`throttle:api`).
- Senhas com hash via `bcrypt` (padrão do Laravel).
- Variáveis sensíveis (chaves, credenciais de banco) carregadas exclusivamente do `.env`, que fica fora do versionamento.
- Tokens emitidos pelo Sanctum têm escopo por dispositivo e podem ser revogados individualmente no logout.
