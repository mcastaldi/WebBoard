//JS functions used in WebBoard
//used for create account panel radio buttons
function headerFunctions(){
	$("#orgAct").hide();
	$("input[name=actType]").on( "change", function() {
		var target = $(this).val();
		$(".chooseActType").hide();
		$("#"+target).show();
	});

	//validation for student account creation
	$("#createStuAct").validate({
		"rules" : {
			"confirmStuPassword" : {
				"equalTo" : "#stuCreatePassword"}
		}
	});
	$("#createOrgAct").validate({
		rules : {
			confirmOrgPassword : {
				equalTo : "#orgCreatePassword"}
		}
	});
	$("#studentForm").validate({});
	$("#orgForm").validate({});


	$("#createAccountLink").on("click", function(){
		$('#loginModal').modal('show');
		$('#loginTabs a:last').tab('show');
	});
}
function hideCreateAccount(){
	$("#studentForm").validate({});
	$("#orgForm").validate({});
	$('#loginTabs a:last').hide();
	$('#createAccountLink').hide();
}

function hideCreateAccount(i){
	$('#loginTabs a:last').hide();
	$('#loginTabs a[href="#loginAsOrg"]').hide();
	$('#createAccountLink').hide();
	$("#studentForm").validate({});
}