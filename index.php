<html>
 <head>
  <title>PHP Test</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
 </head>
 <body>

   <?php require 'logic.php'; ?>
   <?php $a = new Job("a"); ?>
   
   <div class="container">
     <div class="jumbotron">
       <h1>On The Beach: Coding Exercise</h1>
       <p>Hello World</p>
       <?php echo "<p>" . $a->getName() . "</p>"; ?>
     </div>
   </div>

 </body>
</html>
