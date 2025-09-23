create or replace view v_cliente_score as
WITH notas_agrupadas AS (SELECT cod_cli_for                                 AS cliente,
                                COUNT(id) FILTER (WHERE cancelada IS FALSE) AS total_notas_fiscais,
                                 COUNT(id) FILTER (WHERE cancelada IS TRUE)  AS total_notas_canceladas,
                                 SUM(valor_liquido)                          AS total_compras
                         FROM cliente_notas
                         GROUP BY cod_cli_for),
     metricas_cliente AS (SELECT c.codigo                                                                                                    AS cliente,
                                 -- Dados básicos
                                 COALESCE(na.total_notas_fiscais, 0)                                                                          AS notas_fiscais,
                                 COALESCE(na.total_notas_canceladas, 0)                                                                       AS notas_canceladas,
                                 COALESCE(na.total_compras, 0)                                                                                AS total_compras,
                                 COALESCE(COUNT(ct.id), 0)                                                                                    AS titulos,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.saldo = 0 AND status = 'LIQUIDADO'), 0)                                   AS titulos_liquidados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso = 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_em_dia,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso > 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_atraso,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.status = 'ABERTO' and data_baixa is null and data_vencimento < now()), 0) AS titulos_atrasados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso < 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_adiantados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.status = 'ABERTO' and data_vencimento >= now()), 0)                       AS titulos_em_aberto,
                                 COALESCE(ROUND(AVG(ct.hist_dias_atraso) FILTER (WHERE hist_dias_atraso > 0), 1), 0)                          AS media_dias_atraso
                          FROM clientes c
                                   LEFT JOIN cliente_titulos ct ON c.codigo = ct.cliente
                                   LEFT JOIN notas_agrupadas na ON c.codigo = na.cliente
                          GROUP BY c.codigo, na.total_notas_fiscais, na.total_notas_canceladas, na.total_compras),
-- Adiciona cálculos de indicadores de desempenho
     indicadores AS (SELECT cliente,
                            notas_fiscais,
                            notas_canceladas,
                            total_compras,
                            titulos,
                            titulos_liquidados,
                            titulos_pagos_em_dia,
                            titulos_pagos_atraso,
                            titulos_atrasados,
                            titulos_pagos_adiantados,
                            titulos_em_aberto,
                            media_dias_atraso,
                            0       AS pontos_taxa_cancelamento,
                            -- Indicador 2: Taxa de pagamentos em dia (peso 25)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(30 * (titulos_pagos_em_dia::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_pagamentos_em_dia,

                            -- Indicador 3: Taxa de títulos liquidados (peso 20)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(20 * (titulos_liquidados::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_titulos_liquidados,

                            -- Indicador 4: Taxa de pagamentos adiantados (peso 15)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(15 * (titulos_pagos_adiantados::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_pagamentos_adiantados,

                            -- Indicador 5: Taxa de títulos atrasados (peso 20)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(20 * (1 - (titulos_atrasados::numeric / NULLIF(titulos, 0))), 2)
                                END AS pontos_titulos_atrasados,

                            -- Indicador 6: Média de dias de atraso (peso 20)
                            CASE
                                WHEN media_dias_atraso IS NULL THEN 15
                                WHEN media_dias_atraso >= 30 THEN 0
                                ELSE ROUND(15 * (1 - (media_dias_atraso::numeric / 30)), 2)
                                END AS pontos_media_atraso
                     FROM metricas_cliente)
-- Calcula a nota final (soma de todos os pontos, máximo 100)
SELECT cliente,
       notas_fiscais,
       notas_canceladas,
       total_compras,
       titulos,
       titulos_liquidados,
       titulos_pagos_em_dia,
       titulos_pagos_atraso,
       titulos_atrasados,
       titulos_pagos_adiantados,
       titulos_em_aberto,
       media_dias_atraso,
       pontos_taxa_cancelamento,
       pontos_pagamentos_em_dia,
       pontos_titulos_liquidados,
       pontos_pagamentos_adiantados,
       pontos_titulos_atrasados,
       pontos_media_atraso,
       -- Nota final (soma de todos os pontos)
       ROUND(
               pontos_taxa_cancelamento +
               pontos_pagamentos_em_dia +
               pontos_titulos_liquidados +
               pontos_pagamentos_adiantados +
               pontos_titulos_atrasados +
               pontos_media_atraso
           , 2) AS score_cliente,

       -- Classificação baseada no score
       CASE
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 90
               THEN 'A'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 75
               THEN 'B'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 60
               THEN 'C'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 40
               THEN 'D'
           ELSE 'E'
           END  AS classificacao_cliente
FROM indicadores
ORDER BY score_cliente DESC;

create or replace view v_cliente_grupo_score as
WITH notas_agrupadas AS (SELECT cod_grupo_limite                                          AS cod_grupo,
                                COUNT(cliente_notas.id) FILTER (WHERE cancelada IS FALSE) AS total_notas_fiscais,
                                COUNT(cliente_notas.id) FILTER (WHERE cancelada IS TRUE)  AS total_notas_canceladas,
                                SUM(valor_liquido)                                        AS total_compras
                         FROM cliente_notas
                                  join clientes on clientes.codigo = cliente_notas.cod_cli_for
                         GROUP BY cod_grupo_limite),
     metricas_cliente AS (SELECT c.cod_grupo_limite                                                                                           AS cod_grupo,
                                 -- Dados básicos
                                 COALESCE(na.total_notas_fiscais, 0)                                                                          AS notas_fiscais,
                                 COALESCE(na.total_notas_canceladas, 0)                                                                       AS notas_canceladas,
                                 COALESCE(na.total_compras, 0)                                                                                AS total_compras,
                                 COALESCE(COUNT(ct.id), 0)                                                                                    AS titulos,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.saldo = 0 AND status = 'LIQUIDADO'), 0)                                   AS titulos_liquidados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso = 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_em_dia,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso > 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_atraso,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.status = 'ABERTO' and data_baixa is null and data_vencimento < now()), 0) AS titulos_atrasados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.hist_dias_atraso < 0 AND status = 'LIQUIDADO'), 0)                        AS titulos_pagos_adiantados,
                                 COALESCE(COUNT(1) FILTER (WHERE ct.status = 'ABERTO' and data_vencimento >= now()), 0)                       AS titulos_em_aberto,
                                 COALESCE(ROUND(AVG(ct.hist_dias_atraso) FILTER (WHERE hist_dias_atraso > 0), 1), 0)                          AS media_dias_atraso
                          FROM clientes c
                                   LEFT JOIN cliente_titulos ct ON c.codigo = ct.cliente
                                   LEFT JOIN notas_agrupadas na ON c.cod_grupo_limite = na.cod_grupo
                          GROUP BY c.cod_grupo_limite, na.total_notas_fiscais, na.total_notas_canceladas, na.total_compras),
-- Adiciona cálculos de indicadores de desempenho
     indicadores AS (SELECT cod_grupo,
                            notas_fiscais,
                            notas_canceladas,
                            total_compras,
                            titulos,
                            titulos_liquidados,
                            titulos_pagos_em_dia,
                            titulos_pagos_atraso,
                            titulos_atrasados,
                            titulos_pagos_adiantados,
                            titulos_em_aberto,
                            media_dias_atraso,
                            0       AS pontos_taxa_cancelamento,
                            -- Indicador 2: Taxa de pagamentos em dia (peso 25)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(30 * (titulos_pagos_em_dia::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_pagamentos_em_dia,

                            -- Indicador 3: Taxa de títulos liquidados (peso 20)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(20 * (titulos_liquidados::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_titulos_liquidados,

                            -- Indicador 4: Taxa de pagamentos adiantados (peso 15)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(15 * (titulos_pagos_adiantados::numeric / NULLIF(titulos, 0)), 2)
                                END AS pontos_pagamentos_adiantados,

                            -- Indicador 5: Taxa de títulos atrasados (peso 20)
                            CASE
                                WHEN titulos = 0 THEN 0
                                ELSE ROUND(20 * (1 - (titulos_atrasados::numeric / NULLIF(titulos, 0))), 2)
                                END AS pontos_titulos_atrasados,

                            -- Indicador 6: Média de dias de atraso (peso 20)
                            CASE
                                WHEN media_dias_atraso IS NULL THEN 15
                                WHEN media_dias_atraso >= 30 THEN 0
                                ELSE ROUND(15 * (1 - (media_dias_atraso::numeric / 30)), 2)
                                END AS pontos_media_atraso
                     FROM metricas_cliente)
-- Calcula a nota final (soma de todos os pontos, máximo 100)
SELECT cod_grupo,
       notas_fiscais,
       notas_canceladas,
       total_compras,
       titulos,
       titulos_liquidados,
       titulos_pagos_em_dia,
       titulos_pagos_atraso,
       titulos_atrasados,
       titulos_pagos_adiantados,
       titulos_em_aberto,
       media_dias_atraso,
       pontos_taxa_cancelamento,
       pontos_pagamentos_em_dia,
       pontos_titulos_liquidados,
       pontos_pagamentos_adiantados,
       pontos_titulos_atrasados,
       pontos_media_atraso,
       -- Nota final (soma de todos os pontos)
       ROUND(
               pontos_taxa_cancelamento +
               pontos_pagamentos_em_dia +
               pontos_titulos_liquidados +
               pontos_pagamentos_adiantados +
               pontos_titulos_atrasados +
               pontos_media_atraso
           , 2) AS score_cliente,

       -- Classificação baseada no score
       CASE
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 90
               THEN 'A'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 75
               THEN 'B'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 60
               THEN 'C'
           WHEN (pontos_taxa_cancelamento + pontos_pagamentos_em_dia + pontos_titulos_liquidados +
                 pontos_pagamentos_adiantados + pontos_titulos_atrasados + pontos_media_atraso) >= 40
               THEN 'D'
           ELSE 'E'
           END  AS classificacao_cliente
FROM indicadores
ORDER BY score_cliente DESC;

CREATE OR REPLACE VIEW v_clientes_frequencia AS
WITH compras_cliente AS (SELECT cod_cli_for::numeric                                              AS cliente_id,
                                data_mvto,
                                LAG(data_mvto) OVER (PARTITION BY cod_cli_for ORDER BY data_mvto) AS data_compra_anterior
                         FROM cliente_notas
                         WHERE cod_cli_for IS NOT NULL
                           AND cancelada IS FALSE),
     intervalos_compras AS (SELECT cliente_id,
                                   ROUND(AVG(data_mvto - data_compra_anterior), 2) AS frequencia_compra_dias
                            FROM compras_cliente
                            WHERE data_compra_anterior IS NOT NULL
                            GROUP BY cliente_id)
SELECT c.codigo                               AS cliente,
       COALESCE(ic.frequencia_compra_dias, 0) AS frequencia_compra_dias
FROM clientes c
         LEFT JOIN intervalos_compras ic ON c.codigo = ic.cliente_id
ORDER BY frequencia_compra_dias;



CREATE OR REPLACE VIEW v_clientes_frequencia AS
       WITH compras_cliente AS (SELECT cod_cli_for::numeric                                                       AS cliente_id,
                                data_mvto,
                                LAG(data_mvto) OVER (PARTITION BY cod_cli_for ORDER BY data_mvto) AS data_compra_anterior
                         FROM cliente_notas
                         WHERE cod_cli_for IS NOT NULL
                           AND cancelada IS FALSE),
     intervalos_compras AS (SELECT cliente_id,
                                   ROUND(AVG(data_mvto - data_compra_anterior), 2) AS frequencia_compra_dias
                            FROM compras_cliente
                            WHERE data_compra_anterior IS NOT NULL
                            GROUP BY cliente_id)
SELECT c.codigo                               AS cliente,
       COALESCE(ic.frequencia_compra_dias, 0) AS frequencia_compra_dias
FROM clientes c
         LEFT JOIN intervalos_compras ic ON c.codigo = ic.cliente_id
ORDER BY frequencia_compra_dias;