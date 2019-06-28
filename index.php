<html>
 <head>
  <title>PHP Test</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 </head>
 <body>

   <?php require 'jobSorter.php'; ?>
   <?php $jobSorter = new JobSorter; ?>
   <div class="container">
     <div class="jumbotron">
       <h1>On The Beach: Coding Exercise</h1>
       <p>The job relationship: a</p>
       <p>gives</p>
       <?php $jobSorter->input('a'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b c</p>
       <p>gives</p>
       <?php $jobSorter->input('abc'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c</p>
       <p>gives</p>
       <?php $jobSorter->input('ab>cc'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c>f d>a e>b f </p>
       <p>gives</p>
       <?php $jobSorter->input('ab>cc>fd>ae>bf'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b c>c</p>
       <p>gives</p>
       <?php $jobSorter->input('abc>c'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c>f d>a e f>b </p>
       <p>gives</p>
       <?php $jobSorter->input('ab>cc>fd>aef>b'); ?>
       <?php echo "<p>" .
       $jobSorter->orderJobs() .
       "</p>"; ?>
       <hr>
     </div>
   </div>

 </body>

</html>
