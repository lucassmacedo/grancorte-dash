<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Wildside\Userstamps\Userstamps;

class ProspectCliente extends Model
{
    use HasFactory;
    use Userstamps;
    use SoftDeletes;
    use Sortable;

    protected $guarded = [];

    protected $casts = [
        'contatos_referencia' => 'json',
        'approved_at'         => 'datetime',
    ];

    public static $tipo_pessoa = [
        'J' => 'Jurídica',
        'F' => 'Física',
    ];

    public static $tipo_cliente = [
        'R' => 'Revendedor',
        'F' => 'Consumidor',
    ];

    public static $status = [
        0 => 'Rascunho',
        1 => 'Pendente',
        2 => 'Aprovado (Em Análise)',
        3 => 'Reprovado',
    ];

    public static $area_atuacao = [
        '802' => 'Varejo',
        '803' => 'Indústria',
        '804' => 'Food Service',
        '805' => 'Distribuidor',
        '806' => 'AS Regional',
        '807' => 'AS Nacional',
    ];

    public static $natureza = [
        101001 => "MERCADO INTERNO",
        101002 => "MERCADO EXTERNO (EXPORTAÇÃO)"
    ];

    public static $prazo = [0 => 'À Vista', '4' => '4 dias', '7' => '7 dias', '14' => '14 dias'];


    public function getStatusNameAttribute()
    {
        return self::$status[$this->attributes['status']];
    }


    public function getStatusColorAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'secondary';
            case 1:
                return 'info';
            case 2:
                return 'success';
            case 3:
                return 'danger';
        }
    }

    // transform the field 'fundacao' from d/m/Y to Y-m-d before save
    public function setFundacaoAttribute($value)
    {
        if ($value) {
            $this->attributes['fundacao'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }
    }

    public function setFaturamentoAttribute($value)
    {
        if ($value) {
            $this->attributes['faturamento'] = realToFloat($value);
        }
    }

    public function setLimiteCreditoAttribute($value)
    {
        if ($value) {
            $this->attributes['limite_credito'] = realToFloat($value);
        }
    }


    public function getFundacaoAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }
    }

    public function socios()
    {
        return $this->hasMany(ProspectClienteSocio::class, 'cliente_id');
    }


    public static function getAtividades()
    {
        return [
            "8001" => "ACOUGUE",
            "8002" => "ATACADO",
            "8003" => "BAR",
            "8004" => "BOUTIQUE",
            "8005" => "BUFFET",
            "8006" => "CHURRASCARIA",
            "8007" => "DISTIBUIDOR",
            "8008" => "EMPORIO",
            "8009" => "ENTIDADE FILANTROPICA",
            "8010" => "HORTIFRUTI",
            "8011" => "HOTEL",
            "8012" => "IGREJA",
            "8013" => "INDUSTRIA",
            "8014" => "MARMITARIA",
            "8015" => "MERCADO",
            "8016" => "MERCEARIA",
            "8017" => "MINIMERCADO",
            "8018" => "PADARIA",
            "8019" => "PENSAO",
            "8020" => "PIZZARIA",
            "8021" => "RESTAURANTE",
            "8022" => "ROTISSERIA",
            "8023" => "SUPERMERCADO",
            "8024" => "TRAILLER",
            "8025" => "DEFUMADOS",
            "8026" => "CASA DE CARNES",
            "8027" => "ENTREPOSTO",
            "8028" => "LANCHONETE",
            "8029" => "COOPERATIVA",
            "8030" => "COZINHA INDUSTRIAL",
            "8031" => "ARMAZENS",
            "8032" => "HIPERMERCADO",
            "8035" => "ESCOLA",
        ];
    }
}
