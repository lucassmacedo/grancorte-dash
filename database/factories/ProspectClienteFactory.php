<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProspectCliente>
 */
class ProspectClienteFactory extends Factory
{

    protected $model = \App\Models\ProspectCliente::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'codigo_vendedor'             => $this->faker->numberBetween(1, 100),
            'status'                      => $this->faker->numberBetween(0, 1),
            'razao_social'                => $this->faker->company,
            'nome_fantasia'               => $this->faker->companySuffix,
            'cnpj'                        => $this->faker->unique()->numerify('##############'),
            'tipo_pessoa'                 => $this->faker->randomElement(['J', 'F']),
            'inscricao_estadual'          => $this->faker->numerify('#########'),
            'cliente_tipo'                => $this->faker->randomElement(['Tipo A', 'Tipo B', 'Tipo C']),
            'endereco'                    => $this->faker->streetAddress,
            'numero'                      => $this->faker->buildingNumber,
            'complemento'                 => $this->faker->secondaryAddress,
            'bairro'                      => $this->faker->citySuffix,
            'natureza'                    => $this->faker->numberBetween(1, 2),
            'city_id'                     => City::query()->inRandomOrder()->first()->id,
            'telefone_responsavel'        => $this->faker->phoneNumber(),
            'telefone_whatsapp'           => $this->faker->phoneNumber(),
            'cep'                         => $this->faker->postcode,
            'telefone_compras'            => $this->faker->phoneNumber(),
            'fundacao'                    => $this->faker->date('d/m/Y'),
            'telefone_financeiro'         => $this->faker->phoneNumber(),
            'email'                       => $this->faker->email,
            'email_xml'                   => $this->faker->email,
            'prazo'                       => $this->faker->numberBetween(1, 30),
            'area_atuacao'                => $this->faker->jobTitle,
            'limite_credito'              => $this->faker->randomFloat(2, 1000, 100000),
            'atividade'                   => $this->faker->jobTitle,
            'rede'                        => $this->faker->boolean,
            'simples_nacional'            => $this->faker->boolean,
            'faturamento'                 => $this->faker->randomFloat(2, 1000, 1000000),
            'contatos_referencia'         => [
                ['nome' => $this->faker->name, 'telefone' => $this->faker->phoneNumber()],
                ['nome' => $this->faker->name, 'telefone' => $this->faker->phoneNumber()]
            ],
            'rota'                        => $this->faker->word,
            'telefone_recebimento'        => $this->faker->phoneNumber(),
            'observacoes_entrega'         => $this->faker->text(200),
            'endereco_entrega'            => $this->faker->streetAddress,
            'contrato_fornecedor_cliente' => $this->faker->boolean,
            'informacoes_adicionais'      => $this->faker->text(200),
        ];
    }
}
