{template id="page"}
<html>
<body>
    {if test="!$skip_header"}
        <div class="header">{area id="header" size="wide,low"}</div>
    {/if}
    <div class="main">
        {if test="$with_left"}
            <div class="left_col">{area id="left" size="narrow,high"}</div>
        {/if}
        {if test="!$content"}
        <div class="content">
            {area id="content" size="wide,high"}
        </div>
        {/if}
        {if test="$content"}
            {$content}
        {/if}
        {if test="$with_right"}
            <div class="right_col">{area id="right" size="narrow,high"}</div>
        {/if}
    </div>
    {if test="!$skip_footer"}
        <div class="footer">{area id="footer" size="wide,high"}</div>
    {/if}
</body>
</html>
{/template}