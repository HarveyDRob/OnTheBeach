<?php
// To run the test, open a command line window in the root directory and run
// vendor\bin\phpunit JobSorterTest.php

  require 'JobSorter.php';

  class JobSorterTests extends PHPUnit\Framework\TestCase
  {
    private $jobSorter;

    protected function setUp(): void
    {
      $this->jobSorter = new JobSorter();
    }

    protected function tearDown(): void
    {
      $this->jobSorter = NULL;
    }

    public function testInput()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');

      $resultIndependent = $this->jobSorter->getIndependentJobs();
      $resultDependent = $this->jobSorter->getDependentJobs();

      $this->assertEquals('ab>cc>fd>ae>bf', $resultIndependent . $resultDependent);
    }

    public function testSeparateJobsIndependent()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');

      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->getIndependentJobs();

      $this->assertEquals('af', $result);
    }

    public function testSeparateJobsDependent()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');

      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->getDependentJobs();

      $this->assertEquals('b>cc>fd>ae>b', $result);
    }

    public function testCheckCircularTrue()
    {
      $this->jobSorter->input('ab>cc>fd>aef>b');
      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->checkCircular();

      $this->assertEquals(true, $result);
    }

    public function testCheckCircularFalse()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');
      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->checkCircular();

      $this->assertEquals(false, $result);
    }

    public function testCheckSelfDependTrue()
    {
      $this->jobSorter->input('abc>c');
      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->checkSelfDepend();

      $this->assertEquals(true, $result);
    }

    public function testCheckSelfDependFalse()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');
      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->checkSelfDepend();

      $this->assertEquals(false, $result);
    }

    public function testSortJobs()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');
      $this->jobSorter->separateJobs();

      $result = $this->jobSorter->sortJobs();

      $this->assertEquals('afcbde', $result);
    }

    public function testOrderJobs()
    {
      $this->jobSorter->input('ab>cc>fd>ae>bf');

      $result = $this->jobSorter->orderJobs();

      $this->assertEquals('afcbde', $result);
    }

    public function testFillArray()
    {
      $array = [];
      $content = 'b>cc>fd>ae>b';
      $this->jobSorter->fillArray($array, 0, strlen($content), 3, $content);
      $result = $array;

      $this->assertEquals(['b','c','d','e'], $result);
    }
  }

 ?>
