<section class="box big" id="account_articles">
	<h2>
		<img src="{$url}application/themes/admin/images/icons/black16x16/ic_users.png"/>
		Search
	</h2>

	<form style="margin-top:0px;" onSubmit="Accounts.searchAccount(); return false;">
		<input type="text" name="search_accounts" id="search_accounts" placeholder="Search by username or email" style="width:90%;margin-right:5px;"/>
		<input type="submit" value="Search" style="display:inline;padding:8px;" />
	</form>

	<div id="form_accounts_search">
		<!-- results -->
	</div>
</section>