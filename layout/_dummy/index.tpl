{call id="page"}
    {$content}
        <div class="content">
            <div class="index_col_left">{area id="index_left" size="wide,high" /}</div>
            <div class="index_col_right">{area id="index_right" size="wide,high" /}</div>
            <div style="clear:both;"></div>
            {area id="content" size="wide,high" /}
        </div>
    {/$}
    {$with_right select="true" /}
{/call}