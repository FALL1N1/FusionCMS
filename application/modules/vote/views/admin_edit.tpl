<section class="box big">
	<h2>Edit topsite</h2>

	<form onSubmit="Topsites.save(this, {$topsite.id}); return false" id="submit_form">
		<label for="vote_sitename">Site name</label>
		<input type="text" name="vote_sitename" id="vote_sitename" value="{$topsite.vote_sitename}"/>

		<label for="vote_url">Site URL (for postback, append {literal}{account_id}{/literal} to the URL)</label>
		<input type="text" name="vote_url" id="vote_url" placeholder="http://" onChange="Topsites.findImage(this)" value="{$topsite.vote_url}"/>

		<label for="vote_image">Vote site image (will be auto-completed if URL is recognized)</label>
		<input type="text" name="vote_image" id="vote_image" placeholder="(optional)" value="{$topsite.vote_image}"/>

		<label for="hour_interval">Hour interval</label>
		<input type="text" name="hour_interval" id="hour_interval" value="{$topsite.hour_interval}"/>

		<label for="points_per_vote">Voting points</label>
		<input type="text" name="points_per_vote" id="points_per_vote" value="{$topsite.points_per_vote}"/>

		<label for="api_enabled">Enable postback (only some topsites support this - requires additional configuration on the topsite itself)</label>
		<select id="api_enabled" name="api_enabled">
			<option value="0" {if !$topsite.api_enabled}selected{/if}>No</option>
			<option value="1" {if $topsite.api_enabled}selected{/if}>Yes</option>
		</select>

		<input type="submit" value="Save topsite" />
	</form>
</section>