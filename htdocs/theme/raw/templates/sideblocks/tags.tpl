<h3>{str tag="tags"}</h3>
<div class="sidebar-content tags">
{if $data.tags}
  {foreach from=$data.tags item=tag}
  <a class="tag" style="font-size: {$tag->size}em;" href="{$WWWROOT}tags.php?tag={$tag->tag|urlencode}" title="{str tag=numitems arg1=$tag->count}">{$tag->tag|str_shorten_text:20|escape}</a>
  {/foreach}
{else}
  {str tag=youhavenottaggedanythingyet}
{/if}
</div>