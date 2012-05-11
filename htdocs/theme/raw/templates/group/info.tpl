                <ul>
                    <li><label class="groupinfolabel">{str tag=groupadmins section=group}:</label> {foreach name=admins from=$group->admins item=user}
                    <img src="{profile_icon_url user=$user maxwidth=20 maxheight=20}" alt="">
                    <a href="{$WWWROOT}{profile_url($user)}">{$user|display_name}</a>{if !$.foreach.admins.last}, {/if}
                    {/foreach}</li>
                    <li><label class="groupinfolabel">{str tag=grouptype section=group}:</label> {$group->settingsdescription}</li>
                    {if $group->categorytitle}<li><label>{str tag=groupcategory section=group}:</label> {$group->categorytitle}</li>{/if}
                    <li><label class="groupinfolabel">{str tag=Created section=group}:</label> {$group->ctime}</li>
                    <li class="last">
                        {if $group->membercount}<span><label>{str tag=Members section=group}:</label> {$group->membercount}&nbsp;</span>{/if}
                        <span><label>{str tag=Views section=view}:</label> {$group->viewcount}&nbsp;</span>
                        <span><label>{str tag=Files section=artefact.file}:</label> {$group->filecounts->files}&nbsp;</span>
                        <span><label>{str tag=Folders section=artefact.file}:</label> {$group->filecounts->folders}</span>
                    </li>
                </ul>
