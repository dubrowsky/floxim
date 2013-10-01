<div fx:template="listing" class="faq_list">
	{css}listing.css{/css}
    <div fx:template="item" class="faq">
    	<a href="{$url}"><h3>{$question}</h3></a>
    	<div class="answer">
    		{$answer}Answer{/$}
    	</div>
    </div>

    {call id="component_content.pagination" /}
</div>