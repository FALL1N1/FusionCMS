var Topsites = {
	
	/**
	 * General identifier used on #{ID}_count, #add_{ID}, #{ID}_list and #main_{ID}
	 */
	identifier: "topsites",

	/**
	 * The ID of the fusionEditor (like "#news_content"), if any, otherwise put false
	 */
	fusionEditor: false,

	/**
	 * Links for the ajax requests
	 */
	Links: {
		remove: "vote/admin/delete/",
		create: "vote/admin/create/",
		save: "vote/admin/save/",
		move: "vote/admin/move/"
	},

	/**
	 * Removes an entry from the list
	 * @param  Int id
	 * @param  Object element
	 */
	remove: function(id, element)
	{
		var identifier = this.identifier,
			removeLink = this.Links.remove;

		UI.confirm("Do you really want to delete this location?", "Yes", function()
		{
			$("#" + identifier + "_count").html(parseInt($("#" + identifier + "_count").html()) - 1);

			$(element).parents("li").slideUp(300, function()
			{
				$(this).remove();
			});

			$.get(Config.URL + removeLink + id);
		});
	},

	/**
	 * Toggle between the "add" form and the list
	 */
	add: function()
	{
		var id = this.identifier;

		if($("#add_" + id).is(":visible"))
		{
			$("#add_" + id).fadeOut(150, function()
			{
				$("#main_" + id).fadeIn(150);
			});
		}
		else
		{
			$("#main_" + id).fadeOut(150, function()
			{
				$("#add_" + id).fadeIn(150);
			});
		}
	},

	/**
	 * Submit the form contents to the create link
	 * @param Object form
	 */
	create: function(form)
	{
		var values = {csrf_token_name: Config.CSRF};

		$(form).find("input, select").each(function()
		{
			if($(this).attr("type") != "submit")
			{
				values[$(this).attr("name")] = $(this).val();
			}
		});

		if(this.fusionEditor != false)
		{
			values[this.fusionEditor.replace("#", "")] = $(this.fusionEditor).html();
		}

		$.post(Config.URL + this.Links.create, values, function(data)
		{
			console.log(data);
			eval(data);
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
				values[$(this).attr("name")] = $(this).val();
			}
		});

		if(this.fusionEditor != false)
		{
			values[this.fusionEditor.replace("#", "")] = $(this.fusionEditor).html();
		}

		$.post(Config.URL + this.Links.save + id, values, function(data)
		{
			console.log(data);
			eval(data);
		});
	},

	/**
	 * ----------- Module specific code -----------
	 */
	check: function(field)
	{
		var imageURL = false;

		if(/xtremetop100/.test(field.value))
		{
			imageURL = "xtremetop100.jpg";
		}
		else if(/gamesites200/.test(field.value))
		{
			imageURL = "gamesites200.gif";
		}
		else if(/gtop100/.test(field.value))
		{
			imageURL = "gtop100.jpg";
		}
		else if(/mmorpgtoplist/.test(field.value))
		{
			imageURL = "mmorpgtoplist.jpg";
		}
		else if(/openwow/.test(field.value))
		{
			imageURL = "openwow.jpg";
		}
		else if(/top100arena/.test(field.value))
		{
			imageURL = "top100arena.jpg";
		}
		else if(/topgamesites/.test(field.value))
		{
			imageURL = "topgamesites.gif";
		}
		else if(/topg/.test(field.value))
		{
			imageURL = "topg.gif";
			
			if(!/\{account_id\}/i.test($(field).val()))
			{
				var question = "www.topg.org supports postback checking which allows FusionCMS to verify that the user actually does vote. This requires <a style='padding:0px;margin:0px;color:blue;font-weight:normal;float:none;display:inline;' target='_blank' href='https://raxezdev.zendesk.com/entries/22237928-How-to-enable-topg-org-vote-postback'>additional configuration on www.topg.org</a>.<br /><br />Do you want to enable it in FusionCMS?";

				UI.confirm(question, "Yes", function()
				{
					$("#api").fadeIn(100);
					$("#api_enabled").val("1");
					$(field).val(function(index, value)
					{
						return value + "-{account_id}";
					});
				}, false, 380);
			}
		}
		else if(/xtremetopgames/.test(field.value))
		{
			imageURL = "xtremetopgames.png";
		}
		else if(/wowstatus/.test(field.value))
		{
			imageURL = "wowstatus.gif";
		}
		else if(/deadlytop100/.test(field.value))
		{
			imageURL = "deadlytop100.jpg";
		}

		if(imageURL)
		{
			$("#vote_image").val(Config.URL + "application/images/vote_images/" + imageURL);
		}
	}
}