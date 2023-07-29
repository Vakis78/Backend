<?php

$title="";
$message="";
$image_url="";

$save_error_msg="";

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $form_type=isset($_POST["form_type"])?$_POST["form_type"]:"details";
    $title = trim($_POST['title']);
    $message = isset($_POST['message'])?trim($_POST['message']):'';
    $image_url = isset($_POST['image_url'])?trim($_POST['image_url']):'';
    if ($title==""){
        $save_error_msg="Please enter title";
        goto output;
    }
    if ($message==""){
        $save_error_msg="Please enter message";
        goto output;
    }
    try{
        require_once 'FcmNotification.php';
        $topic = "tinhouse_common";
        $notification = new FcmNotification();
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setImage($image_url);
        $notification->setTopic($topic);
        //retrieve token of the user like this:
        //SELECT token FROM pushtoken WHERE user_id=23
        //after this set the token like this:
        //$notification->setToken($token); 
        $notification->setPayload(array("push_type"=>"normal","title"=>$title,"body"=>$message));
        $notification->sendNotification();
    }catch(Exception $ex){
        $save_error_msg="Exception: ".$ex->getMessage();
        goto output;
    }
}
output:
?>

<html>
	<head>
		<title>Send Push</title>
		<script type="text/javascript">
            function saveDetails(){
                console.log("saveDetails");
                if ($("#btnSaveDetails").hasClass("disabled")){
                    return;
                }
                document.getElementById("formNewEvent").submit();
            }
        </script>
	</head>

	<body>
		<div class="content">
            <div class="page-title">
                Send Push
            </div>
            <div class="action-buttons">
            </div>
            <div class="clearfix"></div>
            <!-- new event fields started -->
            <form id="formNewEvent" method="POST">
                <input type="hidden" name="form_type" id="formNewEventType" value="details" />
                <div style="margin-top: 30px;">
                    <?php if($save_error_msg!=""){ ?>
                    <div style="color:#FF0000;font-weight: 600;margin-bottom: 10px;"><?=$save_error_msg;?></div>
                    <?php } ?>
                    <div class="new-event-input big">
                        <div class="label input-label">Title</div>
                        <div class="input-field">
                            <input type="text" maxlength="200" autocomplete="off" id="title" name="title" value="<?=$title;?>" class="form-control" style="width: 600px;" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div style="margin-top: 15px;">
                    <div class="new-event-input big">
                        <div class="label input-label">Image Url</div>
                        <div class="input-field">
                            <input type="text" maxlength="1024" autocomplete="off" id="imageurl" name="image_url" value="<?=$image_url;?>" class="form-control" style="width: 600px;" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div style="margin-top: 15px;">
                    <div class="new-event-input big">
                        <div class="label input-label">Message</div>
                        <div class="input-field">
                            <textarea class="form-control" rows="5" id="message" name="message" style="width: 600px;"><?=$message;?></textarea>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div style="margin-top: 30px;">
                    <button class="common-button primary" id="btnSaveDetails" onclick="saveDetails();">SEND PUSH NOTIFICATION</button>
                </div>
            </form>
            <!-- new event fields end -->

            <!-- spacing in the end of page -->
            <div style="height: 40px;">&nbsp;</div>
        </div>
        <!-- content end -->
        <div class="clearfix"></div>
	</body>
</html>