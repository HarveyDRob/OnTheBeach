<?php

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

  function orderJobs($inputString)
  {

    $outputString = '';
    $independentJobs = $inputString;
    $dependentJobs = '';

    // Search the input for ">", remove it along with one character either side
    // of it and append that to another string, then search the input for ">"
    // again, until all instances of ">" have been removed.
    // strpos returns the position of the searched for substring with the
    // string, unless the substring is not found, in which case it returns false.

    separateJobs($independentJobs, $dependentJobs);

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

    if (strlen($dependentJobs) == 0)
    {
      $outputString = $independentJobs;
    } else
    {
      // At this point we can check for circular dependency, as this only needs
      // the dependent jobs.
      if(checkCircular($dependentJobs))
      {
        // If it is a circular dependency, then we should let the user know that
        // this is an error.
        errorCircular();

        // TODO Fill out errorCircular()
        $outputString = 'This has a circular dependency, and so cannot be sorted.';
      }

      // We can also check for jobs depending on themselves at this point.
      if(checkSelfDepend($dependentJobs))
      {
        errorSelfDepend();

        // TODO Fill out errorSelfDepend()
        $outputString = 'This has a self dependency, and so cannot be sorted.';
      }

      // If we have neither circular dependency nor self dependency, then
      // we can move on to sorting the jobs into the correct order.

      $outputString = sortJobs($independentJobs, $dependentJobs);
    }


    return $outputString;
  }


  function fillArrays(&$arrayA, &$arrayB, &$dJ, &$iJ)
  {
    for ($i = 0; $i < strlen($dJ) - 1; $i += 3)
    {
      array_push($arrayA, substr($dJ, $i, 1));
    }

    for ($i = 0; $i < strlen($iJ); $i++)
    {
      array_push($arrayB, substr($iJ, $i, 1));
    }
  }

  function checkCircular($dJ)
  {
    $isCircular = false;
    $a = array();
    $b = array();
    // $dJ will be at least 3 characters long (e.g. $dJ == "b>c").
    // First we need to push each job that is dependent on another job into $a.
    for ($i = 0; $i < strlen($dJ) -1; $i += 3)
    {
      array_push($a, substr($dJ, $i, 1));
    }

    // If one of these jobs depends on a job which isn't in this array, then
    // remove it from the array.
    for ($i = 2; $i < strlen($dJ); $i+=3)
    {
      $k = substr($dJ, $i, 1);

      if (!array_search($k, $a))
      {
        $n = substr($dJ, $i - 2, 1);
        $nPos = array_search($n, $a);
        unset($a[$nPos]);
      }
    }

    // Now make an array of each job that the jobs in $a are dependent on.
    foreach ($a as $job)
    {
      $jobPos = strpos($dJ, $job);
      array_push($b, substr($dJ, $jobPos + 2));
    }

    // Now we have arrays $a and $b, if they contain the exact same jobs, then
    // we have circular dependency.
    // We can now sort the contents of these arrays alphabetically, then compare
    // them to see if this is the case.
    sort($a);
    sort($b);

    // if they are different lengths then we know straight away that they are
    // different.
    if (count($a) == count($b))
    {
      if (!$isCircular)
      {
        for ($i = 0; $i < count($a); $i++)
        {
          if ($a[$i] != $b[$i])
          {
            $isCircular = true;
          }
        }
      }
    }

    return $isCircular;
  }

  function separateJobs(&$iJ, &$dJ)
  {
    while (strpos($iJ, '>') !== false)
    {
      $target = substr($iJ, strpos($iJ, '>') -1, 3);
      $dJ .= $target;
      $iJ = str_replace($target, '', $iJ);
    }
  }

  function errorCircular()
  {

  }

  function checkSelfDepend($dJ)
  {
    $isSelfDepend = false;

    for ($i = 0; $i < strlen($dJ); $i += 3)
    {
      if(!$isSelfDepend)
      {
        $a = substr($dJ, $i, 1);
        $b = substr($dJ, $i + 2, 1);

        if ($a == $b)
        {
          $isSelfDepend = true;
        }
      }
    }

    return $isSelfDepend;
  }

  function errorSelfDepend()
  {

  }

  function sortJobs($iJ, $dJ)
  {
    $a = array();
    $b = array();

    $result = '';

    fillArrays($a, $b, $dJ, $iJ);

    for ($i = 0; $i < count($a) - 1; $i++)
    {
      $jobPos_ = strpos($dJ, $a[$i] . '>');
      $kappa = substr($dJ, $jobPos_ + 2, 1);

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

 ?>
