<?php

namespace App;

use App\Models\ProdutoPrecoLista;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class SyncAttack
{
    public static function sync_clientes($clientes)
    {
        $role = Role::where(['name' => 'cliente'])->first();

        foreach ($clientes as $cliente) {
            try {
                $cliente = \App\Models\Cliente::updateOrCreate(
                    [
                        'codigo' => $cliente->Cod_cadastro
                    ],
                    [
                        'cod_sisant'       => (int) $cliente->Cod_Sisant ?? null,
                        "codigo_loja"      => $cliente->Codigo_loja,
                        'tipo_cadastro'    => $cliente->Tipo_cadastro,
                        'nome'             => $cliente->Nome_cadastro,
                        'apelido'          => $cliente->Apelido,
                        'cpf_cgc'          => str_replace(' ', '', $cliente->Cpf_Cgc),
                        'rg_ie'            => str_replace(' ', '', $cliente->Rg_IE),
                        'uf'               => $cliente->uf,
                        'cidade'           => $cliente->Cidade,
                        'codigo_municipio' => $cliente->CODIGO_MUNICIPIO,
                        'bairro'           => $cliente->Bairro,
                        'cep'              => $cliente->CEP,
                        'endereco'         => $cliente->Endereco,
                        'numero'           => $cliente->Numero,
                        'telefone'         => $cliente->Fone,
                        'email'            => $cliente->Email,

                        "latitude"                      => $cliente->Latitude,
                        "longitude"                     => $cliente->Longitude,
                        "cod_situacao"                  => str_replace(' ', '', $cliente->Cod_situacao),
                        "cod_lista"                     => (int) $cliente->Cod_lista,
                        "desc_lista"                    => $cliente->Desc_lista,
                        "cod_vendedor"                  => (int) $cliente->Cod_vendedor,
                        "cod_supervisor"                => (int) $cliente->COD_SUPERVISOR > 0 ? $cliente->COD_SUPERVISOR : null,
                        "cod_gerente"                   => (int) $cliente->COD_GERENTE > 0 ? $cliente->COD_GERENTE : null,
                        "cod_forma_cob"                 => $cliente->Cod_forma_cob,
                        "desc_forma_cob"                => $cliente->Desc_forma_cob,
                        "cod_cond_pgto"                 => (int) $cliente->Cod_cond_pgto,
                        "desc_cond_pgto"                => $cliente->Desc_cond_pgto,
                        "prazo_medio"                   => (int) $cliente->Prazo_medio,
                        "cod_area"                      => (int) $cliente->cod_area,
                        "nome_area"                     => $cliente->Nome_area,
                        "cod_ramo"                      => (int) $cliente->cod_ramo,
                        "ramo_atividade"                => $cliente->ramo_atividade,
                        "debitos_grupo"                 => (float) $cliente->debitos_grupo,
                        "limite_consumido"              => (float) $cliente->Limite_consumido,
                        "limite_credito"                => (float) $cliente->Limite_credito,
                        "titulos_individuais_em_aberto" => (float) $cliente->Titulos_individuais_em_aberto,
                        "limite_disponivel"             => $cliente->Limite_disponivel,
                        "adiantamentos_grupo"           => $cliente->Adiantamentos_grupo,
                        "adiantamentos_CNPJ"            => $cliente->Adiantamentos_CNPJ,
                        "cod_grupo_limite"              => (int) $cliente->Cod_grupo_limite,
                        "nome_grupo"                    => $cliente->nome_grupo,
                        "tipo_debito"                   => $cliente->Tipo_debito,
                        "credito_limitado"              => $cliente->Credito_limitado,
                        "perc_desconto"                 => (float) $cliente->Perc_desconto,
                        "data_ultima_compra"            => $cliente->Data_ultima_compra ? Carbon::createFromFormat('d/m/Y', $cliente->Data_ultima_compra)->toDateString() : null,
                        "dias_sem_compra"               => (int) $cliente->Dias_sem_compra,
                        "valor_ultima_compra"           => (float) $cliente->Vlr_ultima_compra,
                        "valor_maior_compra"            => (float) $cliente->Vlr_maior_compra,

                        'created_at' => $cliente->data_cadastro ? Carbon::createFromFormat('d/m/Y', $cliente->data_cadastro)->toDateString() : null,
                        'updated_at' => $cliente->data_alteracao ? Carbon::createFromFormat('d/m/Y', $cliente->data_alteracao)->toDateString() : null,
                    ]

                );
                $cliente->syncRoles($role);
            } catch (\Exception $exception) {
                dump($exception->getMessage());
            }
        }
    }

    public static function sync_users($users)
    {
        $vendedorRole = Role::where(['name' => 'vendedor'])->first();

        foreach ($users as $vendedor) {
            try {
                $user = User::where('codigo', $vendedor->Cod_cadastro)->first();

                $userData = [
                    'codigo'         => $vendedor->Cod_cadastro,
                    'nome'           => $vendedor->Nome_cadastro,
                    'apelido'        => $vendedor->Apelido,
                    'cpf_cgc'        => str_replace(' ', '', $vendedor->Cpf_Cgc),
                    'rg_ie'          => str_replace(' ', '', $vendedor->Rg_IE),
                    'uf'             => $vendedor->uf,
                    'cidade'         => $vendedor->Cidade,
                    'bairro'         => $vendedor->Bairro,
                    'endereco'       => $vendedor->Endereco,
                    'numero'         => $vendedor->Numero,
                    'telefone'       => $vendedor->Telefone,
                    'status'         => str_replace(' ', '', $vendedor->Cod_situacao) == 'A',
                    'is_admin'       => false,
                    "cod_supervisor" => (int) $vendedor->SUPERVISOR > 0 ? $vendedor->SUPERVISOR : null,
                    "cod_gerente"    => (int) $vendedor->GERENTE > 0 ? $vendedor->GERENTE : null,
                    'created_at'     => $vendedor->Data_cadastro,
                    'updated_at'     => $vendedor->Data_alteracao,
                ];
                if (!$user) {
                    $userData['password'] = bcrypt(time() * time());
                    $data_user            = User::create($userData);
                    $data_user->assignRole($vendedorRole);
                } else {
                    $user->update($userData);
                }
            } catch (\Exception $exception) {
                dd($exception->getMessage());
            }
        }
    }

    public static function sync_produtos($produtos)
    {
        $listas = $produtos->pluck('Desc_lista', 'Cod_lista')->unique();
        foreach ($listas as $codigo => $lista) {
            ProdutoPrecoLista::updateOrCreate(
                [
                    'codigo' => $codigo
                ],
                [
                    'descricao' => $lista
                ]
            );
        }


        foreach ($produtos as $produto) {
            try {

                \App\Models\Produto::updateOrCreate(
                    [
                        'codigo' => trim($produto->Cod_produto),
                    ],
                    [
                        'descricao'        => trim($produto->Descricao),
                        'conservacao'      => trim($produto->Conservacao),
                        'sif'              => trim($produto->SIF),
                        'cod_unidade_vda'  => trim($produto->cod_unidade_vda),
                        'cod_unidade_aux'  => trim($produto->Cod_unidade_aux),
                        'desc_unidade_aux' => trim($produto->Desc_unidade_aux),
                        'cod_lista'        => (int) $produto->Cod_lista,
                        'desc_lista'       => $produto->Desc_lista,
                        'peso_padrao'       => $produto->UNIDADE_CALULO_VENDA == '1',
                        'formula_preco'    => (int) $produto->Formula_preco,
                        'status'           => $produto->POSSUI_SALDO_ESTOQUE == 'TRUE',
                        'peso_medio'       => (float) $produto->Peso_medio,
                        'cod_grupo'        => (int) $produto->COD_GRUPO,
                        'desc_grupo'       => $produto->DESC_GRUPO,
                        'venda_por_par'    => $produto->OBRIGA_COMPRA_PAR == 'TRUE'

                    ]
                );


                if ((int) $produto->cod_local > 0) {
                    \App\Models\ProdutoPreco::updateOrCreate(
                        [
                            'codigo'     => trim($produto->Cod_produto),
                            'cod_filial' => (int) $produto->Cod_filial,
                            'cod_local'  => (int) $produto->cod_local,
                            'cod_lista'  => (int) $produto->Cod_lista,
                        ],
                        [
                            'preco_minimo'   => (float) $produto->Preco_minimo,
                            'preco'          => (float) $produto->Preco_v1,
                            'saldo_pri'      => (float) $produto->Saldo_pri,
                            'saldo_aux'      => (int) $produto->Saldo_aux,
                            'preco_anterior' => (float) $produto->VALOR_UNI_ORIGINAL,
                            'promocional'    => $produto->is_promocao == 'TRUE',
                        ]
                    );
                }
            } catch (\Exception $exception) {
                dump($exception->getMessage());
            }
        }
    }

    public static function sync_filiais($filiais)
    {
        foreach ($filiais as $filial) {
            \App\Models\Filial::updateOrCreate(
                [
                    'codigo' => (int) $filial->Cod_filial,
                ],
                [
                    'nome'     => $filial->Nome_filial,
                    'cpf_cgc'  => $filial->Cpf_Cgc,
                    'cidade'   => $filial->Cidade,
                    'endereco' => $filial->Endereco,
                    'bairro'   => $filial->Bairro,
                    'uf'       => $filial->UF,
                    'phone'    => trim($filial->fone),
                ]
            );
        }
    }
}
