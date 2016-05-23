<?php
namespace CucuLab\Remote;

class RemoteModel  implements \JsonSerializable, \Illuminate\Contracts\Support\Arrayable
{


    protected $app;
    protected $url;

    protected $resource;

    protected $token;

    protected $headers = [];

    protected $query;

    protected $guzzleClient;

    public $total = 0;

    public $per_page = 0;

    public $current_page = 0;

    public $last_page = 0;

    public $from = 0;

    public $to = 0;

    public $data;

    protected $paginate = false;

    protected $map = [];

    public function __construct() {
        $this->url = \Config::get('webservices.'.$this->app.'.base_url');
        $this->prepareToken()->prepareGuzzleClient();

    }

    protected function prepareGuzzleClient()
    {
//         dd($this->url);
        $this->guzzleClient = new \GuzzleHttp\Client([
            'headers' => $this->headers,
            'base_uri' => $this->url
        ]);
        return $this->guzzleClient;
    }

    protected function prepareToken()
    {
        if(!empty($this->token))
        {
            $this->headers['token'] = $this->token;
        }
        return $this;
    }

    public function header($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public static function where($column, $value)
    {
        $class  = new static();
        $class->newQuery()->where($column, $value);
        return $class->query;
    }

    public static function all()
    {
        $class  = new static();
        $class->newQuery()->all();
        return $class->query;
    }

    public static function find($id)
    {
        $class  = new static();
        $response = $class->guzzleClient->request('GET',
                $class->resource."/".$id,
                []);
        return $class->getJSONContent($response);
    }

    public function store($data)
    {

    }

    public function update($id, $data)
    {

    }

    public function destroy()
    {

    }

    /**
     * Build Request
     */
    public function newQuery()
    {
        $this->query = new RemoteModelBuilder($this);
        return $this->query;
    }

    protected function prepareQuery()
    {

    }


    public function get()
    {
        $response = $this->guzzleClient->request('GET',
                $this->resource."?".$this->query->toString()."&token=".$this->token,
                []);
        $this->data =  $this->getJSONContent($response);
        if(isset($this->data['per_page']))
        {
            $this->total = $this->data['total'];
            $this->per_page = $this->data['per_page'];
            $this->current_page = $this->data['current_page'];
            $this->last_page = $this->data['last_page'];
            $this->from = $this->data['from'];
            $this->to = $this->data['to'];
            $this->paginate = true;
        }
        return $this;
    }

    protected function getJSONContent($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

//    public function __toString() {
//        if($this->paginate){
//            return json_encode($this->data['data']);
//        }
//        return json_encode($this->data);
//    }

    public function jsonSerialize() {
        if($this->paginate){
            return $this->data['data'];
        }
        return $this->data;
    }

    public function toArray() {
        if($this->paginate){
            return $this->data['data'];
        }
        return $this->data;
    }

    public function mapToDB()
    {
        if(!empty($this->map))
        {

        }
    }

    public function __mapToRepository()
    {

    }

}
