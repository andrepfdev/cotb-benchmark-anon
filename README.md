# php-worker-benchmark

Código e dados do experimento que compara o desempenho de CodeIgniter 4 e Laravel em PHP-FPM, FrankenPHP clássico e FrankenPHP Worker (e Laravel Octane), sob o mesmo orçamento de recursos (1,0 CPU, 512 MB).

## Cenários (`docker-compose.yml`, `CENARIOS.md`)
| Cenário | Descrição |
|---|---|
| A | CodeIgniter 4.7 + PHP-FPM + Nginx |
| B | CodeIgniter 4.7 + FrankenPHP clássico (4 threads) |
| C | CodeIgniter 4.7 + FrankenPHP Worker (4 workers) |
| D | Laravel 13 + Octane + FrankenPHP (4 workers) |
| E | Como B, com número padrão de threads |

Gerador de carga: k6 (`k6/load.js`, carga fechada de 100 VUs; `k6/rate.js`, taxa fixa de 100 req/s). Monitoramento: Prometheus, cAdvisor e Grafana (`monitoring/`). Banco: PostgreSQL 16 (`docker/postgres/init.sql`).

## Estrutura
- `apps/` aplicações de teste (CI4 e Laravel); `docker/` imagens de cada cenário; `k6/` scripts de carga.
- `scripts/run-scenario.sh` uma execução; `scripts/run-completo.sh` baterias (3 execuções por cenário + testes longos); `scripts/run-cotb.sh` coleta complementar (taxa fixa e repetições 4 e 5).
- `scripts/analyze.py`, `scripts/analyze_cotb.py` análises estatísticas (IC95%, Welch com Holm, Mann–Whitney, razão de médias).
- `results/runs`, `results/cotb` dados brutos de cada execução; `results/analysis`, `results/cotb/analysis` tabelas geradas.

## Reproduzir
Requisitos: Docker (Compose v2), ~14 GiB de RAM, `sudo` sem senha para `tee /proc/sys/vm/drop_caches`.

```
./scripts/scaffold.sh                 # prepara as aplicações (Docker-only)
docker compose build
./scripts/run-scenario.sh a load      # uma execução do cenário A (a|b|c|d|e; load|rate|long)
python3 scripts/analyze_cotb.py       # tabelas e figuras a partir de results/
```

Convenção das métricas: vazão = requisições do patamar / duração do patamar (600 s na carga fechada; 480 s na taxa fixa); percentis e latências pelas submétricas `{phase:plateau}` do k6.
