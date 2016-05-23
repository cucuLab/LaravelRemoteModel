<?php
namespace CucuLab\Remote;

class RemoteModelBuilder
{
    protected $whereArray = [];
    protected $whereString = 'fields=';
    protected $pageValue = 1;
    protected $remoteModel = null;

    public function __construct(RemoteModel $remoteModel) {
        $this->remoteModel = $remoteModel;
    }

    public function get()
    {
        return $this->remoteModel->get();
    }



    public function find($id, $columns = ['*'])
    {

    }

    public function where($column, $value = null)
    {
//        $this->whereString .= $column.":".$value;
        $this->whereArray[] = ['column' => $column, 'value'=>$value];
        return $this;
    }

    public function all()
    {
        $this->whereArray = [];
        return $this;
//        return $this->remoteModel->get();
    }

    public function page($page = 1)
    {
        $this->pageValue = $page;
        return $this;
    }

    protected function prepareWhereString()
    {
        foreach ($this->whereArray as $key => $where) {
            $this->whereString .= (($key!=0)?",":""). $where['column'].":".$where['value'];
        }
        return $this;
    }

    public function toString()
    {
        $this->prepareWhereString();
        $pageString = ($this->pageValue > 1)?'&page='.$this->pageValue:'';
        return $this->whereString.$pageString;
    }

}
