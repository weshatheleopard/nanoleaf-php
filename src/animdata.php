<?php

class NanoleafAnimdata {
  public $panels;

  function __construct() {
    $this->panels = Array();
  }

  function ToString() {
    $str = ''.count($this->panels).'';

    foreach($this->panels as $panel) {
      $str.= $panel->ToString().' ';
    }

    return($str);
  }
}

class NanoleafAnimPanel {
  public $id;
  public $frames;

  function __construct($id) {
    $this->id = $id;
    $this->frames = Array();
  }

  function AddFrame($r, $g, $b, $transition_time = 0) {
    $this->frames[] = new NanoleafAnimFrame($r, $g, $b, $transition_time);
  }

  function ToString() {
    $str = '  '.$this->id.' '.count($this->frames).'';
    foreach($this->frames as $frame) {
      $str.= ' '.$frame->ToString().' ';
    }
    return($str);
  }
}

class NanoleafAnimFrame {
  public $r;
  public $g;
  public $b;
  public $w = 0;
  public $transition_time = 0;

  function __construct($r, $g, $b, $transition_time = 0) {
    $this->r = $r;
    $this->g = $g;
    $this->b = $b;
    $this->w = 0;
    $this->delay = $transition_time;
  }

  function ToString() {
    $str = ' '.$this->r.' '.$this->g.' '.$this->b.' '.$this->w.' '.$this->delay;
    return($str);
  }
}

?>