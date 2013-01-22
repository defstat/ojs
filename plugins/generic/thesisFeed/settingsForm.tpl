{**
 * plugins/generic/thesisFeed/settingsForm.tpl
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Thesis Feed plugin settings
 *
 *}
{strip}
{assign var="pageTitle" value="plugins.generic.thesisfeed.displayName"}
{include file="common/header.tpl"}
{/strip}
<div id="thesisFeedSettings">
<div id="description">{translate key="plugins.generic.thesisfeed.description"}</div>

<div class="separator">&nbsp;</div>

<h3>{translate key="plugins.generic.thesisfeed.settings"}</h3>

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#thesisFeedSettingsForm').pkpHandler('$.pkp.controllers.form.FormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="thesisFeedSettingsForm" method="post" action="{plugin_url path="settings"}">
{include file="common/formErrors.tpl"}

<script>
	{literal}
	<!--
		function toggleLimitRecentItems(form) {
			form.recentItems.disabled = !form.recentItems.disabled;
		}
	// -->
	{/literal}
</script>

<table class="data">
	<tr>
		<td class="label" align="right"><input type="radio" name="displayPage" id="displayPage-all" value="all" {if $displayPage eq "all"}checked="checked" {/if}/></td>
		<td class="value">{translate key="plugins.generic.thesisfeed.settings.all"}</td>
	</tr>
	<tr>
		<td class="label" align="right"><input type="radio" name="displayPage" id="displayPage-homepage" value="homepage" {if $displayPage eq "homepage"}checked="checked" {/if}/></td>
		<td class="value">{translate key="plugins.generic.thesisfeed.settings.homepage"}</td>
	</tr>
	<tr>
		<td class="label" align="right"><input type="radio" name="displayPage" id="displayPage-thesis" value="thesis" {if $displayPage eq "thesis"}checked="checked" {/if}/></td>
		<td class="value">{translate key="plugins.generic.thesisfeed.settings.thesis"}</td>
	</tr>
	<tr>
		<td colspan="2"><div class="separator">&nbsp;</div></td>
	</tr>
	<tr>
		<td class="label" align="right"><input type="checkbox" name="limitRecentItems" id="limitRecentItems" value="1" onclick="toggleLimitRecentItems(this.form)"{if $limitRecentItems} checked="checked"{/if}/></td>
		<td class="value">
		{translate key="plugins.generic.thesisfeed.settings.recentThesis1"} <input type="text" name="recentItems" id="recentItems" value="{$recentItems|escape}" {if not $limitRecentItems}disabled="disabled"{/if} size="2" maxlength="90" class="textField" />
		{translate key="plugins.generic.thesisfeed.settings.recentThesis2"}</td>
	</tr>

</table>

<br/>

<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}"/> <input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
{include file="common/footer.tpl"}
