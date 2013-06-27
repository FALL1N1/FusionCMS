var Vote = {
	
	/**
	 * Opens the link and changes the vote now button
	 */
	open: function(id, time)
	{
		// Change the "vote now" button
		$("#vote_field_" + id).html(time + " " + lang("hours_remaining", "vote"));

		// Firefox and IE workaround
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || isIE)
		{
			$.post(Config.URL + "vote/site/", { id: id, csrf_token_name: Config.CSRF, isFirefoxHerpDerp: true }, function(response)
			{
				window.open(response);
			});
		}
	}
}

$(document).ready(function()
{
	if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || isIE)
	{
		setTimeout(function()
		{
			$(".firefox").fadeIn(1000);
		}, 1000);
	}
});