<?php
namespace App\Repositories;

class BaseRepository {

	protected $model;

	public function all(){
		$this->model->all();
	}

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*')) {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

   
}