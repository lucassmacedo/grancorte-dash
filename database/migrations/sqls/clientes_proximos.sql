drop function if exists obter_clientes_proximos(cliente_referencia_id INTEGER, limite INTEGER);
CREATE OR REPLACE FUNCTION obter_clientes_proximos(
    cliente_referencia_id INTEGER,
    limite INTEGER DEFAULT 10,
    p_cod_vendedor INTEGER DEFAULT NULL
)
    RETURNS TABLE
            (
                id               BIGINT,
                nome             VARCHAR(255),
                codigo           BIGINT,
                cpf_cgc          VARCHAR(255),
                apelido          VARCHAR(255),
                endereco         TEXT,
                latitude         NUMERIC,
                longitude        NUMERIC,
                cod_vendedor     integer,
                vendedor         VARCHAR(255),
                cod_grupo_limite integer,
                score_grupo      NUMERIC,
                score_cliente    NUMERIC,
                score            NUMERIC,
                distancia_km     numeric
            )
AS
$$
BEGIN
RETURN QUERY
    WITH cliente_origem AS (SELECT clientes.latitude, clientes.longitude
                                FROM clientes
                                WHERE clientes.id = cliente_referencia_id)
SELECT c.id,
       c.nome,
       c.codigo,
       c.cpf_cgc,
       c.apelido,
       c.endereco || ',' || c.numero || ', ' || c.bairro || ', ' || c.cidade || ' - ' || c.uf as endereco,
       c.latitude,
       c.longitude,
       c.cod_vendedor,
       users.nome                                                                             as vendedor,
       c.cod_grupo_limite,
       score_grupo.score_cliente,
       score_cliente.score_cliente,
       coalesce(score_grupo.score_cliente, score_cliente.score_cliente)                       as score,
       round((6371 * acos(cos(radians(co.latitude))
                              * cos(radians(c.latitude))
                              * cos(radians(c.longitude) - radians(co.longitude))
           + sin(radians(co.latitude))
                              * sin(radians(c.latitude))))::numeric, 2)                       AS distancia_km
FROM clientes c
         LEFT JOIN users ON users.codigo = c.cod_vendedor
         CROSS JOIN cliente_origem co
         LEFT JOIN (SELECT gs.cod_grupo, gs.score_cliente
                    FROM cliente_grupo_scores gs
                             INNER JOIN (SELECT cod_grupo, MAX(created_at) as max_date
                                         FROM cliente_grupo_scores
                                         GROUP BY cod_grupo) recent_gs ON gs.cod_grupo = recent_gs.cod_grupo AND gs.created_at = recent_gs.max_date) score_grupo ON c.cod_grupo_limite = score_grupo.cod_grupo
         LEFT JOIN (SELECT cs.cliente, cs.score_cliente
                    FROM cliente_scores cs
                             INNER JOIN (SELECT cliente, MAX(created_at) as max_date
                                         FROM cliente_scores
                                         GROUP BY cliente) recent_cs ON cs.cliente = recent_cs.cliente AND cs.created_at = recent_cs.max_date) score_cliente ON c.codigo = score_cliente.cliente
WHERE c.id <> cliente_referencia_id -- Exclui o cliente de referência
  AND (p_cod_vendedor IS NULL OR c.cod_vendedor = p_cod_vendedor)
ORDER BY distancia_km ASC
    LIMIT limite;
END;
$$ LANGUAGE plpgsql;