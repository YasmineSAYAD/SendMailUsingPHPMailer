<?php
//PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/OAuth.php';


$mail = new PHPMailer(true);
$mail ->isSMTP ();                                //Set mailer to use SMTP     
$mail ->Host  =  'smtp.gmail.com' ;              // Specify main SMTP server
$mail ->SMTPAuth  =  true ;                     // Enable SMTP authentication 
$mail ->Username  ='test@gmail.com' ;          // User name
$mail ->Password  =  '************';             // Password
$mail->SMTPSecure = 'ssl';                   // Enable tls or ssl encryption  
$mail->Port  =465 ;                         // TCP Port, ssl=>465, tls=> 587
$mail->setFrom ('test@gmail.com');         // sender

//database connection
//database name:dbtest
//table: candidate: idCandidate, firstName, lastName, phone, email, interviewDate
try {
    $db = new PDO('mysql:host=localhost;
    dbname=dbtest; 
    charset=utf8mb4',
     'root',
      '');
    $db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);  
  } catch (PDOException $e) {
    echo "Connection failed : ". $e->getMessage();
  }
  //Select candidate from database
$query =  "select * from candidate";
$stmt = $db->prepare($query);
$stmt->execute();
$row=$stmt->fetchAll();

foreach ($row as $count) {
     //current date
     $currentDate=date('y-m-d');
     // candidate's first name
     $firstName=$count['firstName'];
     //Number of days between the interview date and the current date
    $i=(strtotime($count['interviewDate'])/86400) - (strtotime( $currentDate) /86400);
    
   if($i==1){
    //1 day
    
            //send mail
            // receiver 
            $mail->addAddress ( $count['email']);
            //set the email format to HTM                             
            $mail->isHTML ( true ); 
            //subject of the Mail
            $mail->Subject  =  "No reply" ; 
            // contents of the Mail
            $mail->Body     =  " <div class='emailInterviewDate'>
                                <h1>Hi ".$firstName." </h1>
                                <p> Your interview is tomorrow!</p>
                                <br>
                                </div>";
              if (!$mail ->send()) { 
                  echo "The message has not been sent. " ;
              
              } else { 
                    echo "message has been well sent";
                      
                 } ;
           
              }
            }

  ?>