CREATE OR REPLACE PROCEDURE update_pedidos_status(p_data_entrega DATE)
    LANGUAGE plpgsql
AS
$$
BEGIN
WITH item_counts AS (SELECT pedido_id, COUNT(*) AS item_count
                     FROM pedido_items
                              JOIN pedidos ON pedidos.id = pedido_items.pedido_id
                     WHERE pedidos.data_entrega = p_data_entrega
                     GROUP BY pedido_id),
     corte_counts AS (SELECT pedido_id, COUNT(*) AS corte_count
                      FROM produto_preco_cortes
                      WHERE data_entrega = p_data_entrega
                      GROUP BY pedido_id)
UPDATE pedidos
SET status = 2
WHERE id IN (SELECT item_counts.pedido_id
             FROM item_counts
                      JOIN corte_counts ON item_counts.pedido_id = corte_counts.pedido_id
             WHERE item_counts.item_count = corte_counts.corte_count);
END;
$$;