<?php
include_once("class.contextio.php");
//error_reporting(0);
//ini_set("display_errors",0);
date_default_timezone_set('America/Chicago');
$email = email address here 

//generate random data
$hr1 = strval(rand(70, 180));
$hr2 = strval(rand(70, 180));
$hr3 = strval(rand(70, 180));

$ahr1 = strval(rand(100, (int) $hr1));
$ahr2 = strval(rand(100, (int) $hr2));
$ahr3 = strval(rand(100, (int) $hr3));

$ca1 = strval(rand(150, 600));
$ca2 = strval(rand(150, 600));
$ca3 = strval(rand(150, 600));

$people = array(
    "1" => array(
        "John",
        "Heart Rate (bpm):\n- Max: $hr1\n- Average: $ahr1\nTotal Calories: $ca1"
    ),
    "2" => array(
        "Anya",
        "Heart Rate (bpm):\n- Max: $hr2\n- Average: $ahr2\nTotal Calories: $ca2"
    ),
    "3" => array(
        "Sam",
        "Heart Rate (bpm):\n- Max: $hr3\n- Average: $ahr3\nTotal Calories: $ca3"
    )
);

/* Read the contents of the 'Body' field of the Request. */
$body = $_REQUEST['Body'];
$body = strtolower($body);

$pieces = explode(" ", $body);

$userID = $pieces[0]; // piece1
$period = $pieces[1]; // piece2

if (!$person = $people[$userID]) {
    $name = "User does not exist!";
} else {
    $name = $person[0];
    
    if ($period == "today") {
	// if request was for today, send today report as a message as well as an email
        $data = "Today Report for $name:";
        $data = $data . "\n" . $person[1];
        
        // the message
        $msg = $data;
        
        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg, 70);
        
        // send email
        mail($email, "Today Report for $name", $msg);
        
    } else if ($period == "recent") {
        // if request was for recent, send a message with a link to the summary report
        
	
        $contextIO = new ContextIO('context io account secret', 'context io account key');
        $accountId = "context io account ID";
        
        $args = array(
            'subject' => "Today Report for $name",
            'limit' => 30,
            'include_body' => 1
        );
	 // getting the last 30 reports from email account
        $r    = $contextIO->listMessages($accountId, $args);
        
        $fields = array();
        
        foreach ($r->getData() as $message) {
            $content = $message['body'][0]['content'];
            array_push($fields, $content, date("m-d-Y H:i:s", $message['date_received']));
        }
	 //writing out the reports to userinfo.txt
        $fields = array_reverse($fields);
        $fields = implode("\n", $fields);
        file_put_contents("userinfo.txt", "");
        file_put_contents("userinfo.txt", "\n" . $fields, FILE_APPEND);
        file_put_contents("name.txt", "");
        file_put_contents("name.txt", $name);
	 //use parse.sh to parse userinfo.txt in order to update the summary graph
        $old_path = getcwd();
        chdir($old_path);
        $output = shell_exec('bash parse.sh');
	 //the link to the summary graph
        $data   = "http://23.254.128.69/ActivityReport/report.html";
        
        
    }
}

// now greet the sender
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
  <Message>
    <Body>
      <?php echo $data;?>
    </Body>
  </Message>
</Response>
