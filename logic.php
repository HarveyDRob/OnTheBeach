<?php

  class Job
  {
    private $name;
    private $isDependentOn;

    function __construct($name) {
      $this->name = $name;
    }

    function getName()
    {
      return $this->name;
    }
  }

 ?>
