<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * @important Do not forget install Firestore dependency
 * $ composer require google/cloud-firestore
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require  __DIR__.'/../../vendor/autoload.php';
use Google\Cloud\Firestore\FirestoreClient;

class Firestore {
    protected $db;
    protected $name;
    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        
        $this->db = new FirestoreClient([
            'projectId'     =>  $this->CI->config->item('projectId'),
            'keyFilePath'   =>  $this->CI->config->item('firebase_app_key')
        ]);

    }

    public function getList($collection) {
        try {
            // if (empty($name)) throw new Exception('Document name missing');
            if ($this->db->collection($collection)) {
                return $this->db->collection($collection)->documents();
            } else {
                throw new Exception('Document are not exists');
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getListById($collection,$field,$idArr) {
        if(!$collection || !$field || !$idArr) {
            return false;
        }
        $resultArr      =   [];
        if(is_array($idArr) && count($idArr) > 10) {
            $count           =  0;
            $fieldValue      =  [];
            foreach ($idArr as $key => $value) {
                
                $count++;
                if($count%10 == 0 || end($idArr) == $value) {
                    array_push($fieldValue,$value);
                    $result      =   $this->db->collection($collection)->where($field, "in", $fieldValue )->documents();
                    foreach ($result as $document) {
                        if ($document->exists()) {
                            $resultArr[]        =   $document->data();
                        }
                    }
                    $fieldValue         =   [];
                } else {
                    array_push($fieldValue,$value);
                    continue;
                }
            }
        } else {
            $result      =   $this->db->collection($collection)->where($field, "in", $idArr )->documents();
            foreach ($result as $document) {
                if ($document->exists()) {
                    $resultArr[]        =   $document->data();
                }
            }
        }

        
        return $resultArr;
    }

    public function getListCondition($collection,$condition) {
        try {
            // if (empty($name)) throw new Exception('Document name missing');
            $dbCollection   =   $this->db->collection($collection);
            if ( $dbCollection ) { 
                // if( $condition && is_array($condition) ) {
                    // foreach ($condition as $key => $value) {
                        // $query  =   $dbCollection->where('id','=','2j65hvNL56OLGYisBgkg');
                        $query  =   $dbCollection->where('privateList', 'array-contains-any', [""]);

                    // }    
                    // $this->db->collection($collection)->documents();
                // }
                return $query->documents();
            } else {
                throw new Exception('Document are not exists');
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
        // $citiesRef = $db->collection('cities');
        // $query = $citiesRef->where('state', '=', 'CA');
        // $snapshot = $query->documents();

    }


    /**
     * Get document and all data with checking for exists
     * @param string $name
     * @return array|null|string
     */
    public function getDocument(string $collection,string $name)
    {
        try {
            if (empty($name)) throw new Exception('Document name missing');
            if ($this->db->collection($collection)->document($name)->snapshot()->exists()) {
                return $this->db->collection($collection)->document($name)->snapshot()->data();
            } else {
                throw new Exception('Document are not exists');
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get document with where condition
     * @param string $field
     * @param string $operator
     * @param $value
     * @return array
     */
    public function getWhere(string $collection,string $field, string $operator, $value,$limit=1000)
    {
        $arr    =   [];
        $query  =   $this->db->collection($collection)->where($field, $operator,$value)->limit($limit)->documents()->rows();
        if (!empty($query)) { 
            $modifiedArr        =   [];
            $k                  =   0;
            foreach ($query as $value) {
                $val                =   $value->data();
                $modifiedSort       =   isset($val['modified'])?true:false;
                $arr[$k]            =   $val;
                if($modifiedSort) {
                    $modifiedArr[$k]    =   $val['modified'];
                }
                $k++;
            }
        }
        if(isset($modifiedSort) && $modifiedSort) {
            array_multisort($modifiedArr, SORT_DESC, $arr);
        }

        return $arr;
    }

        /**
     * Get document with where condition
     * @param string $field
     * @param string $operator
     * @param $value
     * @return array
     */
    public function getUserQuestion($userId,$courseId,$limit=1000)
    {
        $arr    =   [];
        $query    =   $this->db->collection("questions")->where("userId", "==",$userId)->limit($limit)->documents()->rows();
        if (!empty($query)) {
            $modifiedArr        =   [];
            $k                  =   0;
            foreach ($query as $value) {
                $val                =   $value->data();
                $modifiedSort       =   isset($val['modified'])?true:false;
                if(!isset($value['courseId']) || $value['courseId'] != trim($courseId)) {
                    continue;
                }
                $arr[$k]            =   $val;
                if($modifiedSort) {
                    $modifiedArr[$k]    =   $val['modified'];
                }
                $k++;
            }
        }
        if(isset($modifiedSort) && $modifiedSort) {
            array_multisort($modifiedArr, SORT_DESC, $arr);
        }

        return $arr;
    }

    public function getCount(string $collection,string $field, string $operator, $value) {
        $docCount       =   0;
        $query = $this->db->collection($collection)->where($field, $operator, $value)->documents()->rows();
        if (!empty($query)) { 
            $docCount       =   count($query);
        }
        return $docCount;   
    }

    /**
    * Create new document with data
    * @param string $name
    * @param array $data
    * @return bool|string
    */
    public function updateDocument(string $collection, string $value, array $data ) {
        $this->db->collection($collection)->document( $value )->set($data);
        return;
    }

    /**
     * Create new document with data
     * @param string $name
     * @param array $data
     * @return bool|string
     */
    public function newDocument(string $collection, array $data = [])
    { 
        try {
            $reference = $this->db->collection($collection)->add($data);
            return $reference->id();
        } catch (Exception $exception){
            return $exception->getMessage();
        }
    }

    /**
    **
    **/
    public function getSubCollection($masterCollection,$collection,$documentId) {
        $result             =   array();

        try {
            $collection         =   $this->db->collection($masterCollection.'/'.$documentId.'/'.$collection);
            $query     =   $collection->documents()->rows();

            if (!empty($query)) { 
                foreach ($query as $value) {
                    $key            =   $value->id();
                    $result[$key]   =   $value->data();
                }
            }
            return $result;
        } catch (Exception $exception){
            return $exception->getMessage();
        }
    }

    /**
    ** add subcollection
    **/
    public function newSubCollection($masterCollection,$collection,$documentId,$data) {
        
        $masterCollection = $this->db->collection($masterCollection.'/'.$documentId.'/'.$collection);

        return $masterCollection->add($data);
    }

    /**
     * Create new collection
     * @param string $name
     * @param string $doc_name
     * @param array $data
     * @return bool|string
     */
    public function newCollection(string $name, string $doc_name, array $data = [])
    {
        try {
            $this->db->collection($name)->document($doc_name)->create($data);
            return true;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Drop exists document in collection
     * @param string $name
     * @return void
     */
    public function dropDocument($collection,string $name)
    {
        $this->db->collection($collection)->document($name)->delete();
    }

    /**
     * Drop exists collection
     * @param string $name
     * @return void
     */
    public function dropCollection($collection,string $name)
    {
        $documents = $this->db->collection($name)->limit(1)->documents();
        while (!$documents->isEmpty()) {
            foreach ($documents as $item) {
                $item->reference()->delete();
            }
        }
    }
}