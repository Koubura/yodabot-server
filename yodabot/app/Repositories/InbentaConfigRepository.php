<?php

namespace App\Repositories;

use App\Models\InbentaConfigModel;
use GuzzleHttp\Client;

class InbentaConfigRepository implements RepositoryInterface {

    public static function all() {
        return InbentaConfigModel::all();
    }

    public static function create(array $data){
        return InbentaConfigModel::create($data);
    }

    public static function update(array $data, $id){
        InbentaConfigModel::findOrFail($id)->update($data);
        return InbentaConfigModel::findOrFail($id);
    }

    public static function delete($id){
        return InbentaConfigModel::destroy($id);
    }

    public static function find($id){
        return InbentaConfigModel::findOrFail($id);
    }

    public static function findByName($name){
        return InbentaConfigModel::where('name','=',$name)->first();
    }
}
