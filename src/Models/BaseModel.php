<?php 

namespace App\Models;

abstract class BaseModel
{
	protected $table;
	protected $column;
	protected $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function getAll()
	{
		$qb = $this->db->createQueryBuilder();
        $qb->select($this->column)
           ->from($this->table);

        $result = $qb->execute();
        return $result->fetchAll();
	}

	public function create(array $data)
    {
        $column = [];
        $paramData = [];
        foreach ($data as $key => $value) {
            $column[$key] = ':'.$key;
            $paramData[$key] = $value;
        }
        $qb = $this->db->createQueryBuilder();
        $qb->insert($this->table)
           ->values($column)
           ->setParameters($paramData)
           // echo $qb->getSQL();
           ->execute();
    }

    //conditional edit
    public function update(array $data, $column, $value)
    {
        $columns = [];
        $paramData = [];

        $qb = $this->db->createQueryBuilder();
        $qb->update($this->table);
        foreach ($data as $key => $values) {
            $columns[$key] = ':'.$key;
            $paramData[$key] = $values;

            $qb->set($key, $columns[$key]);
        }
        $qb->where( $column.'='. $value)
           ->setParameters($paramData)
           ->execute();
    }

    //conditional find
    public function find($column, $value)
    {
        $param = ':'.$column;

        $qb = $this->db->createQueryBuilder();
        $qb->select($this->column)
           ->from($this->table)
           ->where($column . ' = '. $param)
           ->setParameter($param, $value);
           // echo $qb->getSQL();
           // die();
        $result = $qb->execute();

        return $result->fetch();
    }

    //conditional find where deleted = 0
    public function findNotDelete($column, $value)
    {
        $param = ':'.$column;

        $qb = $this->db->createQueryBuilder();
        $qb->select($this->column)
           ->from($this->table)
           ->where($column . ' = '. $param)
           ->andWhere('deleted = 0')
           ->setParameter($param, $value);
           // echo $qb->getSQL();
           // die();
        $result = $qb->execute();

        return $result->fetch();
    }

    public function delete($columnId, $id)
    {
        $param = ':'.$columnId;

        $qb = $this->db->createQueryBuilder();
        $qb->delete($this->table)
           ->where($columnId.' = '. $param)
           ->setParameter($param, $id)
           ->execute();
    }

    public function paginate($page, $query, $limit)
    {
        $qb = $this->db->createQueryBuilder();
        $getRows = $qb->select('COUNT(id) as rows')
                    ->from($this->table)
                    ->execute()
                    ->fetch();
        $perpage = $limit;
        $total = $getRows['rows'];
        $pages = (int) ceil($total / $perpage);

        $data = array(

            'options' => array(
            'default'   => 1,
            'min_range' => 1,
            'max_range' => $pages
            )
        );
        
        $number = (int) $page;
        $range = $perpage * ($number - 1);

        $qb = $this->db->createQueryBuilder();
        $test = $qb->select($this->column)
                   ->from($this->table)
                   ->setFirstResult($range)
                   ->setMaxResults($range + 10)
                   ->execute();
        return $test->fetchAll();
    }
}