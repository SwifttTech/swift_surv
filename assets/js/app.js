// Jquery start
$(document).ready(function() {

        // sidebar - scroll container
        $('.slimscroll-menu').slimscroll({
            height: 'auto',
            position: 'right',
            size: "3px",
            color: '#9ea5ab',
            wheelStep: 5,
            touchScrollStep: 50
        });


    $('aside a').each(function() {
        if ($(this).attr('href') == window.location.pathname) {
            $(this).addClass('active');
        }
    });


    // close humbager
	$(".main-content").click(function() {
		if ($("aside").hasClass("open-menu")) {
			$("aside").removeClass("open-menu");
		}
	});
	$(".close-aside").click(function(event) {
		event.preventDefault();
		$("aside").removeClass("open-menu");
	});

    // humbager
	$(".humbager").click(function(event) {
		event.preventDefault();
		if ($("aside").hasClass("open-menu")) {
			$("aside").removeClass("open-menu");
		} else {
			$("aside").addClass("open-menu");
		}
	});

	// tooltip
	$('[data-toggle="tooltip"]').tooltip();

});

// toogle search
$(".toggle-search").click(function(){
	$(".search-filter").slideToggle();
});

$("body").on("click", ".remove-parent", function(){
	$(this).closest($(this).attr("parent")).remove();
})

// delete an item
$(".delete").click(function(event){
	event.preventDefault();
	swal({
		title: "Are you sure?",
		text: "This item will be deleted and will not recovered.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#ff1a1a",
		confirmButtonText: "Yes, delete it!",
		closeOnConfirm: true
	}, function() {
		toastr.success("Something was deleted successfully.", "Successful!");
	});
});

// schedule class on profile
$(".scheduleclass").submit(function(event){
	event.preventDefault();
	var title = $("#scheduleclass").find("input[name=studentname]").val()+" for a "+$("#scheduleclass").find("select[name=class]").val()+" with "+$("#scheduleclass").find("select[name=instructor]").val();
	calendar.fullCalendar('renderEvent',
                        {
                            title: title,
                            start: $("#scheduleclass").find("input[name=start]").val(),
                            end: $("#scheduleclass").find("input[name=end]").val(),
                    		className: 'primary',
                            allDay: false
                        },
                        true // make the event "stick"
                    );
	calendar.fullCalendar('unselect');
	$("#scheduleclass").modal("hide");
});

// auth page switch pages
$(".auth-switch").click(function(event){
	event.preventDefault();
	$(".register, .forgot, .reset, .login").hide();
	$($(this).attr("show")).show();
});

//Disable every first option
$('option[value="0"]').attr('disabled',true);



/*
 * Toogle SMS Gateway
 */
$("select[name=DEFAULT_SMS_GATEWAY]").change(function(){
	gateway = $(this).val();
	if (gateway === "africastalking") {
		$(".twilio").hide();
		$(".africastalking").show();
	}else if(gateway === "twilio"){
		$(".twilio").show();
		$(".africastalking").hide();
	}
});

/*
 * Toogle reminder SMS & Email
 */
$(".reminders-holder").on("change", ".send_via", function(){
	send_via = $(this).val();
	subject = $(this).closest(".remider-item").find(".email-subject");
	if (send_via === "sms") {
		subject.hide();
		subject.find("input").val('');
		subject.find("input").attr("required", false);
	}else if(send_via === "email"){
		subject.show();
		subject.find("input").attr("required", true);
	}
});



/*
 * add reminder
 */
$(".add-reminder").click(function(){
    $('.collapse').collapse('hide');
    var reminderKey = random(),
          reminderNumber = parseInt($(".reminders-holder").find('.panel').length) + 1;
    $(".reminders-holder").append(`
                                            <!-- reminder -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <span class="delete-reminder remove-parent" parent=".panel" title="Delete reminder"><i class="mdi mdi-delete"></i></span>
                                                    <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse`+reminderKey+`">Reminder #<span class="count">`+reminderNumber+`</span></a></h4>
                                                </div>
                                                <div class="panel-collapse collapse in show" id="collapse`+reminderKey+`">
                                                    <div class="panel-body m-15">
                                                        <div class="remider-item">
                                                            <div class="form-group">
                                                                <div class="row">
                                                                  <div class="col-md-6">
                                                                      <label>Reminder Type</label> 
                                                                      <select class="form-control" name="type[]" required="">
                                                                          <option value="Payment">Payment</option>
                                                                          <option value="Class">Class</option>
                                                                      </select>
                                                                  </div>
                                                                  <div class="col-md-6">
                                                                      <label>Send Via</label> 
                                                                      <select class="form-control send_via" name="send_via[]" required="">
                                                                          <option value="email">Email</option>
                                                                          <option value="sms">SMS</option>
                                                                      </select>
                                                                  </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label>Days</label>
                                                                        <input type="number" class="form-control"  name="days[]" placeholder="Days" value="1" required="" min="0" required="">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label>Timing</label> 
                                                                        <select class="form-control" name="timing[]" required="">
                                                                            <option value="before_due">Before Due Date</option>
                                                                            <option value="after_due">After Due Date</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group email-subject">
                                                                  <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label>Email subject</label> 
                                                                        <input class="form-control" name="subject[]" placeholder="Email subject" required type="text" value="Payment reminder">
                                                                    </div>
                                                               </div>
                                                            </div>
                                                            <div class="form-group">
                                                                  <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label>Message</label> 
                                                                        <textarea class="form-control" name="message[]" required rows="10">Hello [firstname],

We hope you are doing well.
We are am writing to remind you that your payment of $[amountdue] is due on [duedate].
Please settle as soon as possible to avoid class interruption 

Cheers!
`+school+`
                                    </textarea>
                                                                    <p class="help">Supported tags  for Payment: <code>[firstname]</code>, <code>[lastname]</code>, <code>[amountdue]</code>, <code>[duedate]</code> & <code>[course]</code>. Class: <code>[firstname]</code>, <code>[lastname]</code>, <code>[course]</code>, <code>[class]</code>, <code>[classdate]</code>, <code>[classtime]</code> & <code>[instructorname]</code> </p>
                                                                  </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`);
        reminderIndexing();
})

/*
 * delete reminder
 */
$(".reminders-holder").on("click",".delete-reminder",function(){
    $(this).closest(".panel").remove();
    reminderIndexing();
});

/*
 * Number reminder cards
 */
 function reminderIndexing(){
       $(".reminders-holder").find("span.count").each(function(index) { 
        $(this).text(index + 1);
    });
 }

/*
 * Pass sigle variable to input & launch modal
 */
 $(".pass-data").click(function(event){
  event.preventDefault();
  inputName = $(this).attr("input");
  inputValue = $(this).attr("value");
  modal = $(this).attr("modal");
  $("input[name="+inputName+"]").val(inputValue);
  $(modal).modal({show: true, backdrop: 'static', keyboard: false});
 })

 

/*
 * Mark notifications as read
 */
 function readNotifications(url){
    server({
        url: url,
        data: {
            "csrf-token": Cookies.get("CSRF-TOKEN")
        },
        loader: false
    });
 }

/*
 * When course is selected when adding new student
 */
 $("select[name=newcourse]").change(function(){
  course = $(this).val();
  if (course !== '') {
    $(".newamount").show();
  }else{
    $(".newamount").hide();
  }
 })

/*
 * When course is selected when adding new student
 */
 $(".newamount input").keyup(function(){
  amountpaid = $(this).val();
  if (amountpaid > 0) {
    $(".newmethod").show();
  }else{
    $(".newmethod").hide();
  }
 })

/*
 * Edit schedule
 */
function updateSchedule(scheduleid){
  $(".scheduleupdate-holder").html('<div class="loader-box mt-40"><div class="circle-loader"></div></div>');
  $("#scheduleupdate").modal("show");
  var posting = $.post(schedulesUpdateView, { "scheduleid": scheduleid,"csrf-token": Cookies.get("CSRF-TOKEN") });
  posting.done(function (response) {  $(".scheduleupdate-holder").html(response); });
}
