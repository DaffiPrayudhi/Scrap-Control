<?php 

namespace App\Models;

use CodeIgniter\Model;

class PartNumberKompModel extends Model
{
    protected $table = 'part_number_komponen';
    protected $primaryKey = 'id_part_komponen';

    protected $returnType = 'array';

    protected $allowedFields = ['id_part_komponen','model','komponen', 'part_number','harga'];
    protected $useAutoIncrement = true;

    public function getPartNumbersByKomponen($model, $komponen)
    {
        return $this->select('part_number')
                    ->where('model', $model)
                    ->where('komponen', $komponen)
                    ->findAll();
    }

    public function getPartNumbersByModelAndLine($model)
    {
        return $this->select('komponen')
                    ->distinct()
                    ->where('model', $model)
                    ->where('komponen IS NOT NULL')
                    ->findAll();
    }
    


}
