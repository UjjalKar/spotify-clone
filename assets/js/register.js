$(document).ready(function() {

	$("#hideLogin").click(function() {
		$("#loginForm").hide().animate({opacity: "0"});
		$("#registerForm").show().animate({opacity: "1"});
	});

	$("#hideRegister").click(function() {
		$("#loginForm").show().animate({opacity: "1"});
		$("#registerForm").hide().animate({opacity: "0"});
	});
});