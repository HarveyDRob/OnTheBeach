<html>
 <head>
  <title>PHP Test</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 </head>
 <body>

   <?php require 'logic.php'; ?>

   <div class="container">
     <div class="jumbotron">
       <h1>On The Beach: Coding Exercise</h1>
       <p>The job relationship: a</p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('a') .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b c</p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('abc') .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c</p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('ab>cc') .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c>f d>a e>b f </p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('ab>cc>fd>ae>bf') .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b c>c</p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('abc>c') .
       "</p>"; ?>
       <hr>
       <p>The job relationship: a b>c c>f d>a e f>b </p>
       <p>gives</p>
       <?php echo "<p>" .
       orderJobs('ab>cc>fd>aef>b') .
       "</p>"; ?>
       <hr>
     </div>
   </div>

 </body>

</html>
