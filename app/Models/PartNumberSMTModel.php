<?php 

namespace App\Models;

use CodeIgniter\Model;

class PartNumberSMTModel extends Model
{
    protected $table = 'part_number_smt_update';
    protected $primaryKey = 'id_part';

    protected $returnType = 'array';

    protected $allowedFields = ['model', 'part_number','line','harga','scraptype'];
    protected $useAutoIncrement = true;

    public function getUniqueModels()
    {
        return $this->select('model')
                    ->distinct()
                    ->findAll();
    }

    public function getUniqueLines()
    {
        return $this->select('line')
                    ->distinct()
                    ->findAll();
    }

    public function getPartNumbersByModel($model)
    {
        return $this->select('part_number')
                    ->where('model', $model)
                    ->findAll();
    }

    public function getModelsByLine($line)
    {
        return $this->select('model')
                    ->distinct()
                    ->where('line', $line)
                    ->findAll();
    }

    public function getModelsBySMTL1($line)
    {
        return $this->select('model')
                    ->distinct()
                    ->where('line', $line)
                    ->findAll();
    }

    public function getModelsBySMTL2($line)
    {
        return $this->select('model')
                    ->distinct()
                    ->where('line', $line)
                    ->findAll();
    }

    public function getPartNumbersByModelAndLine($model, $line)
    {
        return $this->select('part_number')
                    ->distinct()
                    ->where('model', $model)
                    ->where('line', $line)
                    ->where('part_number IS NOT NULL')
                    ->findAll();
    }

    public function getModelsByFALLine($line)
    {
        return $this->select('model')
                    ->distinct()
                    ->where('line', $line)
                    ->findAll();
    }

    public function getScrapType()
    {
        return $this->select('scraptype')
                    ->where('scraptype IS NOT NULL')
                    ->distinct()
                    ->findAll();
    }

}
