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
      $this->arrayDependentJobs = [];
      $this->arrayCircDependentJobs = [];
      $this->arrayDependedJobs = [];
    }

    // The aim is to take an input of jobs and sort it such that jobs which
    // are dependent on other jobs being completed first are performed after
    // the jobs they're dependent on.
    //
    // To start, we need a way of representing jobs and their dependencies.
    // For example, if we have jobs a, b and c, with the relationships:
    //        a =>
    //        b => c
    //        c =>
    // then we can represent this as the string "ab>cc".
    // This would result in the output "acb", "cab" or "cba".
    //
    // As jobs which aren't depenent on any other jobs and have no jobs dependent
    // on them can be done in any order, we can start by focusing on those with
    // dependencies i.e. jobs which appear either side of the ">" character.
    // In the example above, "a" can appear in any position, all that matters for
    // the correct output is that "c" comes before "b".
    //
    // So the first thing our method has to do is find the characters either side
    // of ">".

    function orderJobs()
    {
      $this->reset();

      // Search the input for ">", remove it along with one character either side
      // of it and append that to another string, then search the input for ">"
      // again, until all instances of ">" have been removed.
      // strpos returns the position of the searched for substring with the
      // string, unless the substring is not found, in which case it returns false.
      $this->separateJobs();

      // At this point $independentJobs is a string which lists jobs which aren't
      // dependent on other jobs, and $dependentJobs is a string which lists each
      // job which is dependent on another job along with the job it is dependent
      // on. In the example above, we would have $inputString == "ab>cc". We
      // this to $independentJobs. The while loop would search $independentJobs
      // for ">", $target would be set to "b>c", and then appended to $dependentJobs
      // and removed from $independentJobs, giving $dependentJobs == "b>c" and
      // $independentJobs == "a".

      // If $dependentJobs is still an empty string, then all jobs are independent
      // and so can be done in any order i.e. if the length of the string
      // $dependentJobs is 0 after sorting the jobs, then we can return the
      // input as the order doesn't matter.

      if (strlen($this->dependentJobs) == 0)
      {
        $this->outputString = $this->independentJobs;
      } else
      {
        // At this point we can check for circular dependency, as this only needs
        // the dependent jobs.
        if ($this->checkCircular())
        {
          // If it is a circular dependency, then we should let the user know that
          // this is an error.
          $this->outputString = 'This has a circular dependency, and so cannot be sorted.';
        }
        // We can also check for jobs depending on themselves at this point.
        else if ($this->checkSelfDepend())
        {
          $this->outputString = 'This has a self dependency, and so cannot be sorted.';
        }
        // If we have neither circular dependency nor self dependency, then
        // we can move on to sorting the jobs into the correct order.
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

    function separateJobs()
    {
      // This function is to split the input string into two separate strings:
      // one with only the jobs which aren't dependent on any other jobs,
      // and the other with the jobs which are dependent on other jobs, and the
      // jobs which they depend on.
      $independentJobs_ = $this->independentJobs;
      $dependentJobs_ = $this->dependentJobs;

      // We search the $independentJobs (which at the start is equal to the
      // input string for '>'. If it is not there, then there are no more
      // dependent jobs and we are finished. If it is there, then we take the
      // substring starting one place before '>' and ending one place after,
      // and add it to $dependentJobs. We then remove that substring from
      // $independentJobs to be left with the independent and dependent jobs
      // in different strings.
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
      // $dJ will be at least 3 characters long (e.g. $dependentJobs == "b>c").
      // First we need to push each job that is dependent on another job into $arrayDependentJobs.
      $this->fillArray($this->arrayDependentJobs, 0, strlen($this->dependentJobs) -1, 3, $this->dependentJobs);

      // Search $arrayDependentJobs for jobs which depend on jobs in $arrayDependentJobs. Add them to $arrayCircDependentJobs.
      for ($i = 2; $i < strlen($this->dependentJobs); $i += 3)
      {
        $k = substr($this->dependentJobs, $i, 1);

        if (array_search($k, $this->arrayDependentJobs) !== false)
        {
          array_push($this->arrayCircDependentJobs, substr($this->dependentJobs, $i - 2, 1));
        }
      }

      // Now make an array of each job that the jobs in $arrayCircDependentJobs are dependent on.
      foreach ($this->arrayCircDependentJobs as $job)
      {
        $job_ = $job . '>';
        $jobPos = strpos($this->dependentJobs, $job_);
        array_push($this->arrayDependedJobs, substr($this->dependentJobs, $jobPos + 2));
      }

      // Now we have arrays $arrayCircDependentJobs and $arrayDependedJobs, if they contain the exact same jobs, then
      // we have circular dependency.
      // We can now sort the contents of these arrays alphabetically, then compare
      // them to see if this is the case.
      sort($this->arrayCircDependentJobs);
      sort($this->arrayDependedJobs);

      // If they are different lengths then we know straight away that they are
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
      // We check each dependent job with the character 2 places after it to
      // see if they are the same character e.g. $dependentJobs = 'c>c'.
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

      // We create 2 arrays, one with the independent jobs, and one with the
      // dependent jobs e.g. $inputString = 'ab>cc>fd>ae>bf'
      // $a = ['b', 'c', 'd', 'e'], $b = ['a', 'f'].

      $this->fillArrays($a, $b, $this->dependentJobs, $this->independentJobs);

      // The independent jobs can be in any order, so we don't need to sort themselves
      // so we focus now on the dependent jobs.
      for ($i = 0; $i < count($a) - 1; $i++)
      {
        // For each entry in the array of dependent jobs, we find it's position
        // in $dependentJobs and then find the job which it is dependent on
        // e.g. $a[$0] . '>' = 'b>', $kappa = 'c'.
        $jobPos_ = strpos($this->dependentJobs, $a[$i] . '>');
        $kappa = substr($this->dependentJobs, $jobPos_ + 2, 1);

        // If the job which it is dependent on is one of the independent jobs,
        // then we can move on, as we will put all the independent jobs before
        // the dependent jobs anyway.
        if (array_search($kappa, $b))
        {
          $i++;
        } else
        {
          // If the job which it is dependent on is already in front of it in $a,
          // then that section is already sorted and we can move on.
          if (array_search($kappa, $a) < $i)
          {
            $i++;
          } else
          {
            // Otherwise, remove the depending job, $kappa, from the array and
             // and move it to before the job which is dependent on it.
            unset($a[array_search($kappa, $a)]);
            array_splice($a, $i, 0, $kappa);
          }
        }
      }

      // Add each of the independent jobs to the result string.
      foreach ($b as $job)
      {
        $result .= $job;
      }

      // Add each of the dependent jobs to the result string.
      foreach ($a as $job)
      {
        $result .= $job;
      }

      return $result;
    }


    function getIndependentJobs()
    {
      return $this->independentJobs;
    }

    function getDependentJobs()
    {
      return $this->dependentJobs;
    }


  }

 ?>
