#!/bin/bash

# Parar containers e remover volume
kool stop
docker volume rm gran_database
kool start

# Configurações
DB_HOST="172.233.19.219"
DB_USER="laravel"
DB_NAME="laravel"
DB_PASSWORD="laravel"
PORT=5432
EXCLUDED_TABLES=("cliente_titulos" "cliente_notas","cliente_notas_items")

# Construir parâmetros de exclusão
EXCLUDE_PARAMS=""
for TABLE in "${EXCLUDED_TABLES[@]}"; do
    EXCLUDE_PARAMS+=" --exclude-table-data=${TABLE}"
done

echo "Fazendo backup do banco $DB_NAME, ignorando os dados: ${EXCLUDED_TABLES[*]}"

# Executar pg_dump DENTRO do container
kool exec database bash -c "PGPASSWORD=$DB_PASSWORD pg_dump -h $DB_HOST -p $PORT -U $DB_USER -d $DB_NAME -F c -f /tmp/$DB_NAME.dump $EXCLUDE_PARAMS"

echo "Importando banco no container local"

# Executar pg_restore DENTRO do container
kool exec database bash -c "PGPASSWORD=laravel pg_restore -h localhost -p 5432 -U laravel -d laravel /tmp/$DB_NAME.dump"

echo "Excluindo arquivo dump"
kool exec database rm /tmp/$DB_NAME.dump

echo "Limpando cache"
kool run artisan cache:clear

echo "Backup finalizado"
