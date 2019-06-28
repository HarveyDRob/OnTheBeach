<?php

  class JobSorter
  {
    private $inputString;
    private $outputString;

    private $independentJobs;
    private $dependentJobs;

    private $isCircular;
    private $isSelfDepend;

    private $arrayDependentJobs;
    private $arrayCircDependentJobs;
    private $arrayDependedJobs;

    function __construct()
    {
      $this->isCircular = false;
      $this->isSelfDepend = false;
    }


    function orderJobs()
    {
      $this->reset();
      $this->separateJobs($this->independentJobs, $this->dependentJobs);

      if (strlen($this->dependentJobs) == 0)
      {
        $this->outputString = $this->independentJobs;
      } else
      {
        if ($this->checkCircular())
        {
          $this->outputString = 'This has a circular dependency, and so cannot be sorted.';
        }
        else if ($this->checkSelfDepend())
        {
          $this->outputString = 'This has a self dependency, and so cannot be sorted.';
        }
        else
        {
          $this->outputString = $this->sortJobs();
        }
      }

      return $this->outputString;
    }



    function input($string)
    {
      $this->inputString = $string;
      $this->independentJobs = $this->inputString;
      $this->dependentJobs = '';
    }

    function reset()
    {
      $this->isCircular = false;
      $this->isSelfDepend = false;
      $this->arrayDependentJobs = [];
      $this->arrayCircDependentJobs = [];
      $this->arrayDependedJobs = [];
    }

    function separateJobs(&$independentJobs_, &$dependentJobs_)
    {
      while (strpos($independentJobs_, '>') !== false)
      {
        $target = substr($independentJobs_, strpos($independentJobs_, '>') -1, 3);
        $dependentJobs_ .= $target;
        $independentJobs_ = str_replace($target, '', $independentJobs_);
      }

      $this->dependentJobs = $dependentJobs_;
      $this->independentJobs = $independentJobs_;
    }

    function fillArray(&$array, $start, $end, $interval, $content)
    {
      for ($i = $start; $i < $end; $i += $interval)
      {
        array_push($array, substr($content, $i, 1));
      }
    }

    function fillArrays(&$arrayA, &$arrayB, &$dJ, &$iJ)
    {
      $this->fillArray($arrayA, 0, strlen($dJ) -1, 3, $dJ);
      $this->fillArray($arrayB, 0, strlen($iJ), 1, $iJ);
    }

    function checkCircular()
    {
      // $dJ will be at least 3 characters long (e.g. $dJ == "b>c").
      // First we need to push each job that is dependent on another job into $a.
      $this->fillArray($this->arrayDependentJobs, 0, strlen($this->dependentJobs) -1, 3, $this->dependentJobs);

      // Search $a for jobs which depend on jobs in $a. Add them to $c.
      for ($i = 2; $i < strlen($this->dependentJobs); $i += 3)
      {
        $k = substr($this->dependentJobs, $i, 1);

        if (array_search($k, $this->arrayDependentJobs) !== false)
        {
          array_push($this->arrayCircDependentJobs, substr($this->dependentJobs, $i - 2, 1));
        }
      }

      // Now make an array of each job that the jobs in $c are dependent on.
      foreach ($this->arrayCircDependentJobs as $job)
      {
        $job_ = $job . '>';
        $jobPos = strpos($this->dependentJobs, $job_);
        array_push($this->arrayDependedJobs, substr($this->dependentJobs, $jobPos + 2));
      }

      // Now we have arrays $a and $b, if they contain the exact same jobs, then
      // we have circular dependency.
      // We can now sort the contents of these arrays alphabetically, then compare
      // them to see if this is the case.
      sort($this->arrayCircDependentJobs);
      sort($this->arrayDependedJobs);

      // if they are different lengths then we know straight away that they are
      // different.
      if (count($this->arrayCircDependentJobs) == count($this->arrayDependedJobs))
      {
        if (!$this->isCircular)
        {
          for ($i = 0; $i < count($this->arrayCircDependentJobs) - 1; $i++)
          {
            if ($this->arrayCircDependentJobs[$i] != $this->arrayDependedJobs[$i])
            {
              $this->isCircular = true;
            }
          }
        }
      } else
      {
        $this->isCircular = false;
      }

      return $this->isCircular;
    }

    function checkSelfDepend()
    {
        for ($i = 0; $i < strlen($this->dependentJobs); $i += 3)
        {
          if(!$this->isSelfDepend)
          {
            $a = substr($this->dependentJobs, $i, 1);
            $b = substr($this->dependentJobs, $i + 2, 1);

            if ($a == $b)
            {
              $this->isSelfDepend = true;
            }
            else
            {
              $this->isSelfDepend = false;
            }
          }
        }

        return $this->isSelfDepend;
    }

    function sortJobs()
    {
      $a = array();
      $b = array();

      $result = '';

      $this->fillArrays($a, $b, $this->dependentJobs, $this->independentJobs);

      for ($i = 0; $i < count($a) - 1; $i++)
      {
        $jobPos_ = strpos($this->dependentJobs, $a[$i] . '>');
        $kappa = substr($this->dependentJobs, $jobPos_ + 2, 1);

        if (array_search($kappa, $b))
        {
          $i++;
        } else
        {
          if (array_search($kappa, $a) < $i)
          {
            $i++;
          } else
          {
            unset($a[array_search($kappa, $a)]);
            array_splice($a, $i, 0, $kappa);
          }
        }
      }

      foreach ($b as $job)
      {
        $result .= $job;
      }

      foreach ($a as $job)
      {
        $result .= $job;
      }

      return $result;
    }

  }

 ?>
