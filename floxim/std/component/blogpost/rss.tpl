<div fx:omit="true" fx:template="listing_rss">
<?='<'.'?xml version="1.0"?'.'>'?>
<?
$_is_admin = false;
?>
    <rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
        <channel>
            {each select="$blog"}
                <title>{$blog_name}{$name editable="false" /}{/$}</title>
                <link>{$blog_url}{$base_url /}{$url editable="false" /}{/$}</link>
                <description>{$blog_description}{$description editable="false"/}{/$}</description>
            {/each}
            <item fx:template="item">
                <title>{$name editable="false"}</title>
                <link>{$base_url}{$url editable="false"}</link>
                <pubDate>{$publish_date editable="false" | 'r'}</pubDate>
                <description>
                    <?ob_start();?>
                        {$anounce editable="false"}
                        <p fx:template="$tags">
                            {$tags_label}Posted under:{/$} 
                            <a fx:template="item" href="{$base_url}{$url editable="false"}">
                                {$name editable="false"}
                            </a>
                            <span fx:omit="true" fx:template="separator">
                                {$rss_tag_separator}, {/$}
                            </span>
                        </p>
                    <?=htmlspecialchars(ob_get_clean());?>
                </description>
            </item>
        </channel>
    </rss>
</div>

<div fx:template="listing_rss_configurator">
    {each select="$blog"}
        Feed title: 
            {%blog_name}
                {$name editable="false" /}
            {/%}
            <br />
        Blog url: 
            {%blog_link}
                {$base_url /}{$url editable="false" /}
            {/%}
            <br />
        Blog description: 
            {%blog_description}
                {$description editable="false"}Put description here{/$}
            {/%}
    {/each}
</div>