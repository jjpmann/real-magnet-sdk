<?php

namespace RealMagnet;

class RealMagnetResponse
{
    protected $status = 'success';

    protected $message;

    protected $error;

    protected $data;

    /**
     * Create a new collection.
     *
     * @param mixed $items
     *
     * @return void
     */
    public function __construct($status, $data, $message = null, $error = false)
    {
        $this->status = $status;
        $this->data = $data;
        $this->error = $error;
        $this->message = $message;
    }

    public static function respond($status, $data, $message = null, $error = false)
    {
        return new static($status, $data, $message, $error);
    }

    public static function success($data, $message = null)
    {
        return self::respond('success', $data, $message);
    }

    public static function error($data, $message = null, $error = true)
    {
        return self::respond('error', $data, $message, $error);
    }

    public static function json($resp)
    {
        $json = json_encode($resp);

        return $json;
    }

    public function __get($var)
    {
        if (isset($this->$var)) {
            return $this->$var;
        }
    }

    /**
     * Convert the response to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    public function isSuccessful()
    {
        return !$this->error;
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->data, $options);
    }
}
/* End of file Response.php */
/* Location: ./system/expressionengine/third_party/motive/libraries/Response.php */
