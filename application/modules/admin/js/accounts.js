var Accounts = {
	/**
	 * Links for the ajax requests
	 */
	Links: {
		save: "admin/accounts/save/",
	},
	
	searchAccount: function() 
	{
		var value = $("#search_accounts").val();
		
		$("#form_accounts_search").html('<center><img src="' + Config.URL + 'application/themes/admin/images/ajax.gif" /><br /><br /></center>');

		$.post(Config.URL + "admin/accounts/search", {value: value, csrf_token_name: Config.CSRF}, function(data)
		{
			$("#form_accounts_search").fadeOut(150, function()
			{
				$(this).html(data).fadeIn(500, function()
				{
					Tooltip.refresh();
				});
			});
		});
	},
	
	/**
	 * Submit the form contents to the save link
	 * @param Object form
	 */
	save: function(form, id)
	{
		var values = {csrf_token_name: Config.CSRF};

		$(form).find("input, select").each(function()
		{
			if($(this).attr("type") != "submit")
			{
				if($(this).attr("type") == "checkbox")
				{
					values[$(this).attr("name")] = this.checked;
				}
				else
				{
					values[$(this).attr("name")] = $(this).val();
				}
			}
		});

		$.post(Config.URL + this.Links.save + id, values, function(data)
		{
			console.log(data);
			eval(data);
		});
	}
}
