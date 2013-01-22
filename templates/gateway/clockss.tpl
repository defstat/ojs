{**
 * templates/gateway/clockss.tpl
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Adapted from lockss.tpl by Martin Paul Eve
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * CLOCKSS Publisher Manifest gateway page.
 * NOTE: This page is not localized in order to provide a consistent interface to CLOCKSS across all OJS installations. It is not meant to be accessed by humans.
 *
 *}
{strip}
{assign var="pageTitleTranslated" value="CLOCKSS Publisher Manifest"}
{include file="common/header.tpl"}
{/strip}

{if $journals}
<h3>Archive of Published Issues</h3>

<ul>
{iterate from=journals item=journal}
	{if $journal->getSetting('enableClockss')}<li><a href="{url journal=$journal->getPath() page="gateway" op="clockss"}">{$journal->getLocalizedName()|escape}</a></li>{/if}
{/iterate}
</ul>
{else}

<p>{if $prevYear !== null}<a href="{url op="clockss" year=$prevYear}" class="action">&lt;&lt; Previous</a>{else}<span class="disabled heading">&lt;&lt; Previous</span>{/if} | {if $nextYear !== null}<a href="{url op="clockss" year=$nextYear}" class="action">Next &gt;&gt;</a>{else}<span class="disabled heading">Next &gt;&gt;</span>{/if}</p>

<h3>Archive of Published Issues: {$year|escape}</h3>

<ul>
{iterate from=issues item=issue}
	<li><a href="{url page="issue" op="view" path=$issue->getBestIssueId($journal)}">{$issue->getIssueIdentification()|strip_unsafe_html|nl2br}</a></li>
{/iterate}
</ul>

{if $showInfo}
<br />

<div class="separator"></div>

<h3>Front Matter</h3>

<p>Front Matter associated with this Archival Unit includes:</p>

<ul>
	<li><a href="{url page="about"}">About the Journal</a></li>
	<li><a href="{url page="about" op="submissions"}">Submission Guidelines</a></li>
	<li><a href="{url page="about" op="contact"}">Contact Information</a></li>
</ul>

<br />

<div class="separator"></div>

<h3>Metadata</h3>

<p>Metadata associated with this Archival Unit includes:</p>

<table class="data">
<tr>
	<td class="label">Journal URL</td>
	<td class="value"><a href="{$journal->getUrl()|escape}">{$journal->getUrl()|escape}</a></td>
</tr>
<tr>
	<td class="label">Title</td>
	<td class="value">{$journal->getLocalizedName()|escape}</td>
</tr>
<tr>
	<td class="label">Publisher</td>
	<td class="value"><a href="{$journal->getSetting('publisherUrl')|escape}">{$journal->getSetting('publisherInstitution')|escape}</a></td>
</tr>
<tr>
	<td class="label">Description</td>
	<td class="value">{$journal->getLocalizedSetting('searchDescription')|escape}</td>
</tr>
<tr>
	<td class="label">Keywords</td>
	<td class="value">{$journal->getLocalizedSetting('searchKeywords')|escape}</td>
</tr>
{if $journal->getSetting('issn')}
<tr>
	<td class="label">ISSN</td>
	<td class="value">{$journal->getSetting('issn')|escape}</td>
</tr>
{/if}
<tr>
	<td class="label">Language(s)</td>
	<td class="value">{foreach from=$locales key=localeKey item=localeName}{$localeName|escape} ({$localeKey|escape})<br />{/foreach}</td>
</tr>
<tr>
	<td class="label">Publisher Email</td>
	<td class="value">{mailto address=$journal->getSetting('contactEmail')|escape encode="hex"}</td>
</tr>
{if $journal->getLocalizedSetting('copyrightNotice')}
<tr>
	<td class="label">Copyright</td>
	<td class="value">{$journal->getLocalizedSetting('copyrightNotice')|nl2br}</td>
</tr>
{/if}
{if $journal->getLocalizedSetting('openAccessPolicy')}
<tr>
	<td class="label">Rights</td>
	<td class="value">{$journal->getLocalizedSetting('openAccessPolicy')|nl2br}</td>
</tr>
{/if}
</table>
{/if}

{/if}

<br /><br />

<div style="text-align: center; width: 250px; margin: 0 auto">
	<a href="http://clockss.org/"><img src="http://board.clockss.org/images/clockss_logo_114x110.jpg" style="border: 0;" alt="CLOCKSS" /></a>
	<br />
	clockss system has permission to collect, preserve, and serve this Archival Unit.
		
	<br /><br />
	
	<a href="http://pkp.sfu.ca/"><img src="{$baseUrl}/lib/pkp/templates/images/pkp.gif" style="border: 0;" alt="The Public Knowledge Project" /></a>
	<br />
	Open Journal Systems was developed by the Public Knowledge Project.
</div>

{include file="common/footer.tpl"}

