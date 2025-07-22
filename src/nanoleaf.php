<?php

class Nanoleaf {
  protected $server;
  protected $port;
  protected $token;
  public $debug;

  function __construct($token, $server, $port = 16021) {
    $this->server = $server;
    $this->port = $port;
    $this->token = $token;
    $this->debug = false;
    $this->socket = NULL;
  }

  function OpenSocket() {
    $this->socket = fsockopen($this->server, $this->port, $errno, $errstr, 15);
  }

  function ReadHeader() {
    $header = $str = '';

    while(!feof($this->socket)) {
      $chr = @fread($this->socket, 1);

      $str .= $chr;
      if(strlen($str) > 4) { $str = substr($str, 1, 4); }

      if($str == "\r\n\r\n") break;
      $header .= $chr;
    }

    if($this->debug) { print("<<< {$header}\n"); }
    return($header);
  }

  function ReadBody($header) {
    $content_length = $this->GetContentLength($header);

    if($content_length < 1) return('');

    $body = @fread($this->socket, $content_length);
    if($this->debug) { print("<<< {$body}\n"); }
    return($body);
  }

  function SendRequest($data) {
    fputs($this->socket, $data);
    if($this->debug) { print(">>> {$data}"); }
  }

  function ReadResponse() {
    // The hardware does not respect the "Connection: close" header and keeps the connection open.
    // In order to ensure that we only read the first Response and bail out, we need to
    // mind its Content-Length: and quit once we read it all.
    $header = $this->ReadHeader($this->socket);
    return($this->ReadBody($header));
  }

  function GetContentLength($header) {
    $arr = explode("\r\n", $header);

    foreach($arr as $str) {
      if(substr($str, 0, 16) =='Content-Length: ') {
        return(intval(substr($str, 16)));
      }
    }
    return(0);
  }

  function Communicate($data) {
    if($this->socket == NULL) $this->OpenSocket();
    $this->SendRequest($data);
    return($this->ReadResponse());
  }

  function ApiGet($local_path) {
    $data = "GET /api/v1/{$this->token}/{$local_path} HTTP/1.1\r\n\r\n";

    $body = $this->Communicate($data);
  }

  function ApiPut($local_path, $post_data) {
    $data =
      "PUT /api/v1/{$this->token}/{$local_path} HTTP/1.1\r\n".
      'Content-Length: '.strlen($post_data)."\r\n".
      "Content-Type: application/json\r\n".
      "\r\n".
      $post_data;

    $body = $this->Communicate($data);
  }

  function Pair() {
    $this->OpenSocket();

    $data = "POST /api/v1/new HTTP/1.1\r\n\r\n";

    $body = $this->Communicate($data);
    preg_match('/{"auth_token":"(.+)"}/', $body, $matches);
    return($matches[1]);
  }
}
?>